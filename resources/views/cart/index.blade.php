<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cart') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (request('message'))
                        <div class="alert alert-warning">
                            {{ request('message') }}
                        </div>
                    @endif
                    @if ($cart && count($cart->courses) > 0)
                        @foreach ($cart->courses as $course)
                            <div class="bg-light mb-3 p-2 d-flex justify-content-between align-items-center">
                                <h6>{{ $course->name }}
                                    <small class="text-primary">({{ $course->price() }})</small>
                                </h6>
                                <a href="{{ route('removeFromCart', $course) }}" class="btn btn-sm btn-danger">Remove</a>
                            </div>
                        @endforeach

                        <div class="bg-light mb-3 p-2 d-flex justify-content-between align-items-center">
                            <h6>Total
                                <small class="text-primary">({{ $cart->total() }})</small>
                            </h6>
                            <div>
                                {{-- @if (Auth::user()->hasDefaultPaymentMethod())
                                    <a href="#"
                                    <a href="{{ route('direct.paymentMethod.oneClick') }}"
                                        class="btn btn-sm btn-info">One
                                        Click
                                        Checkout</a>
                                @endif --}}
                               
                                {{-- <a href="{{ route('checkout') }}"
                                    class="btn btn-sm btn-success">Checkout</a> --}}
                                {{-- <a href="{{ route('checkout.enableCoupons') }}"
                                    class="btn btn-sm btn-info">Checkout with Coupons</a> --}}
                                {{-- <a href="{{ route('checkout.nonStripeProducts') }}"
                                    class="btn btn-sm btn-warning">Checkout with Non-Stripe Products</a> --}}
                                {{-- <a href="{{ route('checkout.lineItems') }}"
                                    class="btn btn-sm btn-primary">Checkout with Line Items</a>
                                <a href="{{ route('checkout.guest') }}"
                                    class="btn btn-sm btn-secondary">Checkout as Guest</a> --}}
                                <a href="{{ route('direct.paymentMethod.oneClick') }}"
                                    class="btn btn-sm btn-info">One Click Checkout</a>
                                <a href="{{ route('direct.paymentMethod') }}"
                                    class="btn btn-sm btn-success">Checkout with Payment Method</a>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">Your Cart Is Empty</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>