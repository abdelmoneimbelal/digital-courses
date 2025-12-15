<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;

class PaymentMethodCheckoutController extends Controller
{
    public function index()
    {
        return view("checkout.payment-method");
    }

    public function post(Request $request)
    {
        // if ($request->payment_method) {
        //     Auth::user()->updateOrCreateStripeCustomer();
        //     Auth::user()->updateDefaultPaymentMethod($request->payment_method);
        // }

        $cart = Cart::session()->first();
        $amount = $cart->courses->sum('price');
        $paymentMethod = $request->payment_method;
        $payment = Auth::user()->charge($amount, $paymentMethod, [
            'return_url' => route('home', ['message' => 'Payment successful!']),
            'metadata' => [
                'cart_id' => $cart->id,
                'user_id' => Auth::user()->id
            ]
        ]);
        
        return redirect()->route('home', ['message'=> 'Payment successful!']);
        
        // if ($payment->status == 'succeeded') {
        //     $order = Order::create([
        //         'user_id' => Auth::user()->id,
        //     ]);
        //     $order->courses()->attach($cart->courses->pluck('id')->toArray());
        //     $cart->delete();
        //     return redirect()->route('home', ['message'=> 'Payment successful!']);
        // }
    }
}
