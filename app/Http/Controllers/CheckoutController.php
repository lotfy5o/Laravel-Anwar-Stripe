<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function checkout()
    {
        $cart = Cart::session()->first();

        $prices = $cart->courses->pluck('stripe_price_id')->toArray();

        $sessionOptions = [
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
            // 'billing_address_collection' => 'required',

            // 'phone_number_collection' => [
            //     'enabled' => true,
            // ],

            'metadata' => [
                'cart_id' => $cart->id,
            ],
        ];

        $customerOptions = [
            'my code' => 1111111,
        ];

        // $payment = Auth::user()->checkout($prices, $sessionOptions, $customerOptions);
        // dd($payment);

        return Auth::user()->checkout($prices, $sessionOptions, $customerOptions);
    }

    public function success(Request $request)
    {
        $session = $request->user()->stripe()->checkout->sessions->retrieve($request->get('session_id'));

        if ($session->payment_status === 'paid') {

            $order = Order::create([
                'user_id' => $request->user()->id,
            ]);

            $cart = Cart::findOrFail($session->metadata->cart_id);

            $order->courses()->attach($cart->courses->pluck('id')->toArray());

            $cart->delete();

            return redirect()->route('home', ['message' => 'Payment Successfull']);
        }
    }

    public function cancel(Request $request)
    {
        $session = $request->user()->stripe()->checkout->sessions->retrieve($request->get('session_id'));
        return redirect()->route('home', ['message' => 'Payment Failed']);
    }


    // public function cancel(Request $request)
    // {
    //     $sessionId = $request->get('session_id');

    //     if (empty($sessionId)) {
    //         return redirect()->route('home', ['message' => 'Payment Failed: Missing session ID']);
    //     }

    //     $session = $request->user()->stripe()->checkout->sessions->retrieve($sessionId);

    //     return redirect()->route('home', ['message' => 'Payment Failed']);
    // }



    public function enableCoupons()
    {
        $cart = Cart::session()->first();

        $prices = $cart->courses->pluck('stripe_price_id')->toArray();

        $sessionOptions = [
            'success_url' => route('checkout.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel') . '?session_id={CHECKOUT_SESSION_ID}',

            // use ->allowPromotionCodes() to enable promotion codes instead of the next line
            // 'allow_promotion_codes' => true,

            'metadata' => [
                'cart_id' => $cart->id,
            ],
        ];

        $customerOptions = [
            'my code' => 1111111,
        ];

        $payment = Auth::user()->checkout($prices, $sessionOptions, $customerOptions);
        dd($payment);

        return Auth::user()
            // ->withPromotionCode('promo_1QqIew4F6yFHOnyiLSxuHxn7')
            // ->allowPromotionCodes()
            ->checkout($prices, $sessionOptions, $customerOptions);
    }

    public function nonStripeProduct()
    {
        $cart = Cart::session()->first();
        $amount = $cart->courses->sum('price');

        $sessionOptions = [
            'success_url' => route('checkout.success', ['message' => 'Payment Successfull']) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel', ['message' => 'Payment Failed']) . '?session_id={CHECKOUT_SESSION_ID}',


        ];

        return Auth::user()->checkoutCharge($amount, 'course bundel', 1, $sessionOptions);
    }

    public function lineItems()
    {
        $cart = Cart::session()->first();

        $courses = $cart->courses->map(function ($course) {
            return [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $course->name,
                    ],
                    'unit_amount' => $course->price, // Amount in cents (e.g., $50.00)
                ],
                'quantity' => 1,
            ];
        })->toArray();

        $sessionOptions = [
            'success_url' => route('checkout.success') . "?session_id={CHECKOUT_SESSION_ID}",
            'cancel_url' => route('checkout.cancel') . "?session_id={CHECKOUT_SESSION_ID}",

            'line_items' => $courses,
        ];

        $payment =  Auth::user()->checkout(null, $sessionOptions);
        dd($payment);

        return Auth::user()->checkout(null, $sessionOptions);
    }
}
