<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentMethodCheckoutController extends Controller
{
    public function index()
    {
        return view("checkout.payment-method");
    }

    public function post(Request $request)
    {
        DB::beginTransaction();
        if ($request->payment_method) {
            Auth::user()->updateOrCreateStripeCustomer();
            Auth::user()->updateDefaultPaymentMethod($request->payment_method);
        }
        try {
            $cart = Cart::session()->first();
            $amount = $cart->courses->sum('price');
            $paymentMethod = $request->payment_method;
            $payment = Auth::user()->charge($amount, $paymentMethod, [
                'return_url' => route('direct.paymentMethod.success'),
                'metadata' => [
                    'cart_id' => $cart->id,
                    'user_id' => Auth::user()->id
                ]
            ]);

            if ($payment->status == 'succeeded') {
                $order = Order::create([
                    'user_id' => Auth::user()->id,
                ]);
                $order->courses()->attach($cart->courses->pluck('id')->toArray());
                $cart->delete();
                DB::commit();
                return redirect()->route('home', ['message' => 'Payment successful!']);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('home', ['message' => 'Payment failed!']);
        }
    }

    /**
     * معالجة العودة من Stripe بعد الدفع (مثل 3D Secure).
     * Stripe يضيف payment_intent في الرابط عند التوجيه.
     */
    public function success(Request $request)
    {
        $paymentIntentId = $request->query('payment_intent');
        if (!$paymentIntentId) {
            return redirect()->route('home', ['message' => 'Invalid return from payment.']);
        }

        $paymentIntent = Auth::user()->stripe()->paymentIntents->retrieve($paymentIntentId);
        if ($paymentIntent->status !== 'succeeded') {
            return redirect()->route('home', ['message' => 'Payment was not completed.']);
        }

        $cartId = $paymentIntent->metadata->cart_id ?? null;
        if (!$cartId) {
            return redirect()->route('home', ['message' => 'Payment successful!']);
        }

        $cart = Cart::find($cartId);
        if (!$cart) {
            return redirect()->route('home', ['message' => 'Payment successful!']);
        }
        if ($cart->user_id && $cart->user_id != Auth::id()) {
            return redirect()->route('home', ['message' => 'Payment successful!']);
        }

        $order = Order::create([
            'user_id' => Auth::user()->id,
        ]);
        $order->courses()->attach($cart->courses->pluck('id')->toArray());
        $cart->delete();

        return redirect()->route('home', ['message' => 'Payment successful!']);
    }

    public function oneClick(Request $request)
    {
        if (Auth::user()->hasDefaultPaymentMethod()) {
            $cart = Cart::session()->first();
            $amount = $cart->courses->sum('price');
            $paymentMethod = Auth::user()->defaultPaymentMethod()->id;
            $payment = Auth::user()->charge($amount, $paymentMethod, [
                'return_url' => route('home', ['message' => 'Payment successful!']),
            ]);
            
            if ($payment->status == 'succeeded') {
                $order = Order::create([
                    'user_id' => Auth::user()->id,
                ]);
                $order->courses()->attach($cart->courses->pluck('id')->toArray());
                $cart->delete();
                return redirect()->route('home', ['message'=> 'Payment successful!']);
            }
        }

    }
}
