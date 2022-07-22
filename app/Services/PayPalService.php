<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\General\CollectionHelper;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Services\PayPalService;
use App\Traits\ConsumesExternalServices;
use Cache;
use Carbon\Carbon;
use App\Models\{PaymentHistory,UserPlan};

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
        $user = JWTAuth::parseToken()->authenticate();
        $expiresAt = Carbon::now()->addMinutes(10);
  

        $data =json_encode($request->all());
        
        $order = $this->createOrder($request->value, $request->currency);
        $orderLinks = collect($order->links);
        $approve = $orderLinks->where('rel', 'approve')->first();
        Cache::put('approvalId', $order->id, $expiresAt);
        Cache::put('plan_id', json_decode($data)->plan_id, $expiresAt);
        Cache::put('value', json_decode($data)->value, $expiresAt);
        Cache::put('user_id', json_decode($data)->user_id, $expiresAt);
       
        return  $approve;//redirect($approve->href);
    }
    public function handlePaymentAnuncio(Request $request)
    {
        $user = JWTAuth::parseToken()->authenticate();
        $expiresAt = Carbon::now()->addMinutes(10);
        $data =json_encode($request->all());
        $order = $this->createOrder($request->value, $request->currency);
        $orderLinks = collect($order->links);
        $approve = $orderLinks->where('rel', 'approve')->first();
        Cache::put('approvalId', $order->id, $expiresAt);
        Cache::put('anuncio_id', json_decode($data)->anuncio_id, $expiresAt);
        Cache::put('user_id', json_decode($data)->user_id, $expiresAt);
        return  $approve;//redirect($approve->href);
    }

    public function handleApproval()
    {
        $user_id = Cache::get('user_id');
        $plan_id = Cache::get('plan_id');
        $value = Cache::get('value');
        $approvalId = Cache::get('approvalId');

        if ($approvalId) {
            $this->savePaymentPlan($user_id,$plan_id,$value,$approvalId);
            $payment = $this->capturePayment($approvalId);
            return view('landing.aprobado');
        }else{
            return view('landing.cancelado');
        }
        
    }
    public function handleApprovalAnuncio()
    {
        $user_id = Cache::get('user_id');
        $anuncio_id = Cache::get('anuncio_id');
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

    public function savePaymentPlan($user_id,$plan_id,$value,$approvalId)
    {
        $user_plan = new UserPlan;
        $user_plan->user_id = $user_id;
        $user_plan->plan_id = $plan_id;
        $user_plan->status = 'Aprobado';
        $user_plan->date_end_at = Carbon::now()->addDays(30);
        
        $user_plan->save();

        $payment_history = new PaymentHistory;
        $payment_history->mount = $value;
        $payment_history->status = 'Aprobado';
        $payment_history->user_id = $user_id;
        $payment_history->way_to_pay = 'PayPal';
        $payment_history->transaction_number = $approvalId;

        $payment_history->save();
    }
}