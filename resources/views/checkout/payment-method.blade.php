<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Direct Checkout - Payment Method') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form action="{{ route('direct.paymentMethod.post') }}" method="post" id="form">
                        @csrf

                        <input type="hidden" name="payment_method" id="payment_method">

                        <!-- Stripe Elements Placeholder -->
                        <div id="card-element"></div>

                        <button id="card-button" class="btn btn-sm btn-primary mt-3" type="button">
                            Process Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Stripe
        const stripe = Stripe(@json(env('STRIPE_KEY')));
        const elements = stripe.elements();
        const cardElement = elements.create('card', {
            hidePostalCode: true
        });
        cardElement.mount('#card-element');

        // Handle Payment
        document.getElementById('card-button').addEventListener('click', async (e) => {
            const {
                paymentMethod,
                error
            } = await stripe.createPaymentMethod(
                'card', cardElement
            );

            if (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error Payment Method',
                    text: error.message || 'An error occurred while processing the payment',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
                console.log(error);
                // Display "error.message" to the user...
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Payment Method Success',
                    // text: 'Payment Method Success',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true
                });
                console.log(paymentMethod);
                document.getElementById('payment_method').value = paymentMethod.id;
                document.getElementById('form').submit();
                // The card has been verified successfully...
            }
        });
    </script>
</x-app-layout>