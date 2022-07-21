<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\General\CollectionHelper;
use Illuminate\Support\Facades\Auth;
use App\Services\PayPalService;
use App\Traits\ConsumesExternalServices;
use Carbon\Carbon;

class PayPalService
{
    use ConsumesExternalServices;

    protected $baseUri;

    protected $clientId;

    protected $clientSecret;

    protected $plans;

    public function __construct()
    {
        $this->baseUri = config('services.paypal.base_uri');
        $this->clientId = config('services.paypal.client_id');
        $this->clientSecret = config('services.paypal.client_secret');
        $this->plans = config('services.paypal.plans');
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
        $credentials = base64_encode("{$this->clientId}:{$this->clientSecret}");

        return "Basic {$credentials}";
    }

    public function handlePayment(Request $request)
    {
        dd(json_decode($request));
        $order = $this->createOrder($request->value, $request->currency);
        $orderLinks = collect($order->links);
        $approve = $orderLinks->where('rel', 'approve')->first();
        session()->put('approvalId', $order->id);
        session()->put('plan_id', $request->plan_id);
        session()->put('user_id', $request->user_id);
       
        return  $approve;//redirect($approve->href);
    }

    public function handleApproval()
    {
        $user_id = session()->get('user_id');
        $plan_id = session()->get('plan_id');
        if (session()->has('approvalId')) {
            dd('pase correcto');
            $approvalId = session()->get('approvalId');
            $payment = $this->capturePayment($approvalId);                  
            return null;
        }

        return view('landing.aprobado');
    }

    public function createOrder($value, $currency)
    {
        return $this->makeRequest(
            'POST',
            '/v2/checkout/orders',
            [],
            [
                'intent' => 'CAPTURE',
                'purchase_units' => [
                    0 => [
                        'amount' => [
                            'currency_code' => strtoupper($currency),
                            'value' => round($value * $factor = $this->resolveFactor($currency)) / $factor,
                        ]
                    ]
                ],
                'application_context' => [
                    'brand_name' => config('app.name'),
                    'shipping_preference' => 'NO_SHIPPING',
                    'user_action' => 'PAY_NOW',
                    'return_url' => route('approval'),
                    'cancel_url' => route('cancelled'),
                ]
            ],
            [],
            $isJsonRequest = true,
        );
    }

    public function capturePayment($approvalId)
    {
        return $this->makeRequest(
            'POST',
            "/v2/checkout/orders/{$approvalId}/capture",
            [],
            [],
            [
                'Content-Type' => 'application/json',
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