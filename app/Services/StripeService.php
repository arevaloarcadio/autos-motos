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
        $user = JWTAuth::parseToken()->authenticate();
        $expiresAt = Carbon::now()->addMinutes(10);
        $request->validate([
            'paymentMethod' => 'required',
        ]);

        $intent = $this->createIntent($request->value, $request->currency, $request->paymentMethod);
       
        Cache::put('paymentIntentId', $intent->id, $expiresAt);
        Cache::put('plan_id', $request->plan_id, $expiresAt);
        Cache::put('user_id', $request->user_id, $expiresAt);

        return redirect()->route('stripe-approval');
    }

    public function handleApproval()
    {
            $user_id = Cache::get('user_id');
            $plan_id = Cache::get('plan_id');
            $paymentIntentId = Cache::get('paymentIntentId');
            if ($paymentIntentId) {
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
                    $this->savePaymentPlan($user_id,$plan_id,$amount,$transactionId);
                    return view('landing.aprobado');
                }else{
                    return view('landing.cancelado');
                }
            }else{
                return view('landing.cancelado');
            }

       
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


    public function resolveFactor($currency)
    {
        $zeroDecimalCurrencies = ['JPY'];

        if (in_array(strtoupper($currency), $zeroDecimalCurrencies)) {
            return 1;
        }

        return 100;
    }
}