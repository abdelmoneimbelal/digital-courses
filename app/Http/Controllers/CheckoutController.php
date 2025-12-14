<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
class CheckoutController extends Controller
{
    public function checkout()
    {
        $cart = Cart::session()->first();
        $prices = $cart->courses->pluck('stripe_price_id')->toArray();

        $sessionOptions = [
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel').'?session_id={CHECKOUT_SESSION_ID}',
            'metadata' => [
                'cart_id' => $cart->id
            ]
        ];

        // $sessionOptions = [
        //     // 'success_url' => route('home', ['message' => 'Payment successful!']),
        //     'success_url' => route('home', ['message' => 'Payment successful!']),
        //     'cancel_url' => route('home', ['message' => 'Payment failed!']),
        //     'billing_address_collection' => 'required',
        //     'phone_number_collection' => [
        //         'enabled' => true,
        //     ],
        //     'metadata' => [
        //         'cart_id' => $cart->id
        //     ]
        // ];

        $customerOptions = [
            'metadata' => [
                'my_code' => 1231564654
            ]
        ];

        // dd(Auth::user()->checkout($prices, $sessionOptions, $customerOptions));

        return Auth::user()->checkout($prices, $sessionOptions, $customerOptions);
    }

    public function success(Request $request)   
    {
        $session = $request->user()->stripe()->checkout->sessions->retrieve($request->get('session_id'));

        if ($session->payment_status == 'paid') {
            $cart = Cart::findOrFail($session->metadata->cart_id);
    
            $order = Order::create([
                'user_id' => $request->user()->id,
            ]);
    
            $order->courses()->attach($cart->courses->pluck('id')->toArray());
    
            $cart->delete();
    
            return redirect()->route('home', ['message' => 'Payment successful!']);
        }

    }

    public function cancel(Request $request)   
    {
        $session = $request->user()->stripe()->checkout->sessions->retrieve($request->get('session_id'));
    }
}
