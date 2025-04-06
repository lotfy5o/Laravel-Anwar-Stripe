<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($cart && count($cart->courses) > 0)
                        @foreach ($cart->courses as $course)
                            <div
                                class="bg-light mb-3 p-2 d-flex justify-content-between align-items-center">
                                <h6> {{ $course->name }}
                                    <small class="text-primary"> ({{ $course->price() }}) </small>
                                </h6>

                                <a class="btn btn-danger">Remove</a>
                            </div>
                        @endforeach
                        <div
                            class="bg-light mb-3 p-2 d-flex justify-content-between align-items-center">
                            <h6> Total
                                <small class="text-success"> ({{ $cart->total() }}) </small>
                            </h6>

                            <a href="{{ route('direct.paymentMethod') }}"
                                class="btn btn-success">Checkout</a>
                        </div>
                        @if (Auth::check() && Auth::user()->hasDefaultPaymentMethod())
                            <div class="bg-light mb-3 p-2 d-flex justify-content-end">
                                <a href="{{ route('direct.paymentMethod.oneClick') }}"
                                    class="btn btn-info">One Click Checkout</a>
                            </div>
                        @endif
                    @else
                        <div class="alert alert-info"> Your Cart Is Empty </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
