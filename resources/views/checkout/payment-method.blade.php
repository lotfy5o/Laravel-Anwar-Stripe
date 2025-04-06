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
                    <form action="{{ route('direct.subscription.post') }}" method="POST"
                        id="form">
                        @csrf

                        <input type="hidden" id="plan_id" name="plan_id"
                            value="{{ $plan->id }}">

                        <input type="hidden" id="payment_method" name="payment_method">

                        <!-- Stripe Elements Placeholder -->
                        <div id="card-element"></div>

                        <button id="card-button" class="btn btn-sm  btn-primary mt-3"
                            type="button">
                            Process Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize Stripe
        const stripe = Stripe(
            'pk_test_51R4J0iQOOQD5fiJ8Uzfm0KDAty6SKKFpStcrRU6DDjJBwOBanID8W3WS2cQ2ViLXR24I1hJKDz8XzEUA5dFFs5bS00IUNkNGDh'
        );
        // Stripe(@json(env('STRIPE_KEY')));

        const elements = stripe.elements();
        const cardElement = elements.create('card');

        cardElement.mount('#card-element');


        // Handle Payment
        const cardHolderName = document.getElementById('card-holder-name');
        const cardButton = document.getElementById('card-button');

        cardButton.addEventListener('click', async (e) => {
            const {
                paymentMethod,
                error
            } = await stripe.createPaymentMethod(
                'card', cardElement
            );

            if (error) {
                alert('error');
                console.log(error);
            } else {

                console.log(paymentMethod);
                document.getElementById('payment_method').value = paymentMethod.id;
                document.getElementById('form').submit();
            }
        });
    </script>
</x-app-layout>
