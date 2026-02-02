<?php

use App\Models\Cart;
use App\Models\Course;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentMethodCheckoutController;

Route::get('/', function () {
    $courses = Course::all();
    return view('home', get_defined_vars());
})->name('home');

// Courses
Route::controller(CourseController::class)->group(function () {
    Route::get('/courses/{course:slug}', 'show')->name('courses.show');
});

// Cart Management
Route::controller(CartController::class)->group(function () {
    Route::get('/cart', 'index')->name('cart.index');
    Route::get('/addToCart/{course:slug}', 'addtoCart')->name('addtoCart');
    Route::get('/removeFromCart/{course:slug}', 'removeFromCart')->name('removeFromCart');
});

// \Laravel\Cashier\Checkout
Route::controller(CheckoutController::class)->group(function () {
    Route::get('/checkout', 'checkout')->middleware('auth')->name('checkout');
    Route::get('/checkout/enableCoupons', 'enableCoupons')->middleware('auth')->name('checkout.enableCoupons');
    Route::get('/checkout/nonStripeProducts', 'nonStripeProducts')->middleware('auth')->name('checkout.nonStripeProducts');
    Route::get('/checkout/lineItems', 'lineItems')->middleware('auth')->name('checkout.lineItems');
    Route::get('/checkout/guest', 'guest')->name('checkout.guest');
    Route::get('/checkout/success', 'success')->middleware('auth')->name('checkout.success');
    Route::get('/checkout/cancel', 'cancel')->middleware('auth')->name('checkout.cancel');
});

// Direct Integration - Payment Method
Route::controller(PaymentMethodCheckoutController::class)->group(function () {
    Route::get('/direct/paymentMethod', 'index')->middleware('auth')->name('direct.paymentMethod');    
    Route::post('/direct/paymentMethod/post', 'post')->middleware('auth')->name('direct.paymentMethod.post');    
    Route::get('/direct/paymentMethod/success', 'success')->middleware('auth')->name('direct.paymentMethod.success');    
    Route::get('/direct/paymentMethod/oneClick', 'oneClick')->middleware(['auth', 'protectOneClickCheckout'])->name('direct.paymentMethod.oneClick');    
});


// Direct Integration - Payment Intent
Route::controller(PaymentIntentController::class)->group(function () {
    Route::get('/direct/paymentIntent', 'index')->middleware('auth')->name('direct.paymentIntent');    
    Route::post('/direct/paymentIntent/post', 'post')->middleware('auth')->name('direct.paymentIntent.post');  
});


// Direct Integration - setup Intent
Route::controller(SetupIntentController::class)->group(function () {
    Route::get('/direct/setupIntent', 'index')->middleware('auth')->name('direct.setupIntent');    
    Route::post('/direct/setupIntent/post', 'post')->middleware('auth')->name('direct.setupIntent.post');  
});



Route::post('stripe/webhook', [WebhookController::class, 'handleWebhook'])->name('cashier.webhook');

Route::get('/dashboard', action: function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
