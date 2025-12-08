<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Course;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return view('cart.index');
    }

    public function addToCart(Course $course)
    {
        $cart = Cart::firstOrCreate([
            'session_id' => session()->getId(),
            'user_id' => auth()->user() ? auth()->user()->id : null,
        ]);

        $cart->courses()->syncWithoutDetaching($course);
        
        return back();
    }

    public function removeFromCart(Course $course)
    {
        $cart = Cart::session()->first();

        abort_unless($cart, 404);
        
        $cart->courses()->detach($course);

        return back();
    }
}
