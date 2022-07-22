<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StripeService;

class StripeController extends Controller
{
    public function pay(Request $request)
    {
        $rules = [
            'value' => ['required', 'numeric', 'min:5'],
            'currency' => ['required'],
            'plan_id' => ['required'],
            'user_id' => ['required'],
        ];
        $request->validate($rules);
        $paymentPlatform = resolve(StripeService::class);
        return $paymentPlatform->handlePayment($request);
    }
    public function approval()
    {
        $data="arepa";
        $paymentPlatform = resolve(StripeService::class);

        return $data;
    }
    public function cancelled()
    {
        return view('landing.cancelado');
    }

   
}