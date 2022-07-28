<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\StripeService;
use App\Models\Billing;

class StripeController extends Controller
{
    public function pay(Request $request)
    {
        $rules = [
            'value' => ['required', 'numeric', 'min:0.5'],
            'currency' => ['required'],
            'plan_id' => ['required'],
            'user_id' => ['required'],
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:15',
            'country' => 'nullable|string|max:255'
        ];
        $request->validate($rules);

        $billing = new Billing;
        $billing->name = $request->name;
        $billing->email = $request->email;
        $billing->phone = $request->phone;
        $billing->country = $request->country;
        $billing->user_id = $request->user_id;

        $billing->save();

        $paymentPlatform = resolve(StripeService::class);
        return $paymentPlatform->handlePayment($request);
    }
    public function approval()
    {
        $paymentPlatform = resolve(StripeService::class);

        return $paymentPlatform->handleApproval();
    }
    public function cancelled()
    {
        return view('landing.cancelado');
    }

   
}