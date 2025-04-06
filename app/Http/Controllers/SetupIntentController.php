<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SetupIntentController extends Controller
{
    public function index()
    {
        $setupIntent = Auth::user()->createSetupIntent();
        return view('checkout.setup-intent', get_defined_vars());
    }

    public function post(Request $request)
    {
        // I am using a query scope to get the cart
        $cart = Cart::session()->first();

        // I am using a where clause to get the first cart that belongs to the session id
        $amount = $cart->courses->sum('price');

        $paymentMethod = $request->payment_method;

        Auth::user()->createOrGetStripeCustomer();

        $options = [
            'return_url' => route('home', ['message' => 'Payment Successfull']),
        ];

        $payment = Auth::user()->charge($amount, $paymentMethod, $options);

        if ($payment->status = 'succeeded') {
            // I am using a query scope to get the cart
            $cart = Cart::session()->first();

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
