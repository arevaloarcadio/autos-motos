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
        ];

        $request->validate($rules);
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
        return redirect()
            ->route('home')
            ->withErrors('You cancelled the payment');
    }

   
}