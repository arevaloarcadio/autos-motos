<?php

namespace App\Services;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use App\Helpers\General\CollectionHelper;
use App\Traits\ConsumesExternalServices;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class StripeService
{
    use ConsumesExternalServices;

    protected $key;

    protected $secret;

    protected $baseUri;

    protected $plans;

    public function __construct()
    {
        $this->baseUri = config('services.stripe.base_uri');
        $this->key = config('services.stripe.key');
        $this->secret = config('services.stripe.secret');
        $this->plans = config('services.stripe.plans');
    }

    public function resolveAuthorization(&$queryParams, &$formParams, &$headers)
    {
        $headers['Authorization'] = $this->resolveAccessToken();
    }

    public function decodeResponse($response)
    {
        return json_decode($response);
    }

    public function resolveAccessToken()
    {
        return "Bearer {$this->secret}";
    }

    public function handlePayment(Request $request)
    {
        $request->validate([
            'payment_method' => 'required',
        ]);

        $intent = $this->createIntent($request->value, $request->currency, $request->payment_method);
       
        

        return $intent;
    }

    public function handleApproval()
    {
        if (session()->has('paymentIntentId')) {
            
            $paymentIntentId = session()->get('paymentIntentId');
            $plan_id = session()->get('plan_id');
            $cupon_id = session()->get('cupon_id');

            $confirmation = $this->confirmPayment($paymentIntentId);
       
            if ($confirmation->status === 'requires_action') {
                $clientSecret = $confirmation->client_secret;

                return view('stripe.3d-secure')->with([
                    'clientSecret' => $clientSecret,
                ]);
            }

            if ($confirmation->status === 'succeeded') {
                $transactionId=$confirmation->id;
                $name = $confirmation->charges->data[0]->billing_details->name;
                $currency = strtoupper($confirmation->currency);
                $amount = $confirmation->amount / $this->resolveFactor($currency);
                $plan =Plan::find($plan_id);
                if ($cupon_id) {
                    $cupon=Cupon::find($cupon_id);
                    $cupon->cantidad=$cupon->cantidad-1;
                    $cupon->save();
                    $cuponHistory = CuponHistory::create([
                        'precio_pago' => $amount,
                        'plan_id' => $plan_id,
                        'cupon_id' => $cupon_id,
                    ]);
                }
                $solicitud = PlanUser::create([
                    'comprobante' => $transactionId,
                    'plan_id' => $plan_id,
                    'available' => $plan->stock,
                    'user_id' => Auth::user()->id,
                    'tipo' => "Stripe",
                ]);
                       
            $idiomas=Idioma::get();

                return view('landing.Comprar.Completado', compact('transactionId','idiomas'));
            }
        }

        return redirect()
            ->route('home')
            ->withErrors('We were unable to confirm your payment. Try again, please');
    }

    public function handleSubscription(Request $request)
    {
        $customer = $this->createCustomer(
            $request->user()->name,
            $request->user()->email,
            $request->payment_method
        );

        $subscription = $this->createSubscription(
            $customer->id,
            $request->payment_method,
            $this->plans[$request->plan]
        );

        if ($subscription->status == 'active') {
            session()->put('subscriptionId', $subscription->id);

            return redirect()->route(
                'subscribe.approval',
                [
                    'plan' => $request->plan,
                    'subscription_id' => $subscription->id,
                ],
            );
        }

        $paymentIntent = $subscription->latest_invoice->payment_intent;

        if ($paymentIntent->status === 'requires_action') {
            $clientSecret = $paymentIntent->client_secret;

            session()->put('subscriptionId', $subscription->id);

            return view('stripe.3d-secure-subscription')->with([
                'clientSecret' => $clientSecret,
                'plan' => $request->plan,
                'paymentMethod' => $request->payment_method,
                'subscriptionId' => $subscription->id,
            ]);
        }

        return redirect()->route('subscribe.show')
            ->withErrors('We were unable to activate your subscription. Try again, please.');
    }

    public function validateSubscription(Request $request)
    {
        if (session()->has('subscriptionId')) {
            $subscriptionId = session()->get('subscriptionId');

            session()->forget('subscriptionId');

            return $request->subscription_id == $subscriptionId;
        }

        return false;
    }

    public function createIntent($value, $currency, $paymentMethod)
    {
        return $this->makeRequest(
            'POST',
            '/v1/payment_intents',
            [],
            [
                'amount' => round($value * $this->resolveFactor($currency)),
                'currency' => strtolower($currency),
                'payment_method' => $paymentMethod,
                'confirmation_method' => 'manual',
            ],
        );
    }

    public function confirmPayment($paymentIntentId)
    {
        return $this->makeRequest(
            'POST',
            "/v1/payment_intents/{$paymentIntentId}/confirm",
        );
    }

    public function createCustomer($name, $email, $paymentMethod)
    {
        return $this->makeRequest(
            'POST',
            '/v1/customers',
            [],
            [
                'name' => $name,
                'email' => $email,
                'payment_method' => $paymentMethod,
            ],
        );
    }

    public function createSubscription($customerId, $paymentMethod, $priceId)
    {
        return $this->makeRequest(
            'POST',
            '/v1/subscriptions',
            [],
            [
                'customer' => $customerId,
                'items' => [
                    ['price' => $priceId],
                ],
                'default_payment_method' => $paymentMethod,
                'expand' => ['latest_invoice.payment_intent']
            ],
        );
    }

    public function resolveFactor($currency)
    {
        $zeroDecimalCurrencies = ['JPY'];

        if (in_array(strtoupper($currency), $zeroDecimalCurrencies)) {
            return 1;
        }

        return 100;
    }
}