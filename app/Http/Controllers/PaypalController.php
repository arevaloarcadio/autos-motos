<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

use App\Services\PayPalService;
class PaypalController extends Controller
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
        $paymentPlatform = resolve(PayPalService::class);
        return $paymentPlatform->handlePayment($request);
    }
    public function approval()
    {
        $paymentPlatform = resolve(PayPalService::class);

        return $paymentPlatform->handleApproval();
    }
    public function cancelled()
    {
     
        return view('landing.cancelado');
    }
}