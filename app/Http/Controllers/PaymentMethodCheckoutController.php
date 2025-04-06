<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentMethodCheckoutController extends Controller
{
    public function index()
    {
        return view('checkout.payment-method');
    }

    public function post(Request $request)
    {

        // if ($request->payment_method) {
        //     Auth::user()->updateOrCreateStripeCustomer();
        //     // the addPaymentMethod takes the id of the payment method 
        //     // that I passed through the hidden input field in the form
        //     // this function stores the payment method in the database and u can see it in the stripe dashboard
        //     /* Auth::user()->addPaymentMethod($request->payment_method); */

        //     // this function makes the new payment method the default payment method
        //     Auth::user()->updateDefaultPaymentMethod($request->payment_method);
        // }

        $cart = Cart::session()->first();

        // I am using a where clause to get the first cart that belongs to the session id
        $amount = Cart::where('session_id', session()->getId())->first()->courses->sum('price');

        $paymentMethod = $request->payment_method;


        $options = [
            'return_url' => route('home', ['message' => 'Payment Successfull']),
            'metadata' => [
                'cart_id' => $cart->id,
                'user_id' => Auth::user()->id
            ]
        ];

        $payment = Auth::user()->charge($amount, $paymentMethod, $options);

        if ($payment->status = 'succeeded') {
            // I am using a query scope to get the cart


            // the order is made using just the id of the user
            $order = Order::create([
                'user_id' => Auth::user()->id
            ]);

            // to get the courses that will be put inside the orders_courses table
            // I will get them from the cart
            $order->courses()->attach($cart->courses->pluck('id')->toArray());


            $cart->delete();

            // 'message' is a query parameter that will be passed to the home page
            return redirect()->route('home', ['message' => 'Payment Successfull']);
        }
    }

    public function oneClick(Request $request)
    {
        if (Auth::user()->hasDefaultPaymentMethod()) {

            $paymentMethod = Auth::user()->defaultPaymentMethod()->id;

            // I am using a query scope to get the cart
            $cart = Cart::session()->first();

            $amount = $cart->courses->sum('price');

            $options = [
                'return_url' => route('home', ['message' => 'Payment Successfull']),
            ];


            $payment = Auth::user()->charge($amount, $paymentMethod, $options);

            if ($payment->status = 'succeeded') {


                // the order is made using just the id of the user
                $order = Order::create([
                    'user_id' => Auth::user()->id
                ]);

                // to get the courses that will be put inside the orders_courses table
                // I will get them from the cart
                $order->courses()->attach($cart->courses->pluck('id')->toArray());

                $cart->delete();

                // 'message' is a query parameter that will be passed to the home page
                return redirect()->route('home', ['message' => 'Payment Successfull']);
            }
        }
    }
}
