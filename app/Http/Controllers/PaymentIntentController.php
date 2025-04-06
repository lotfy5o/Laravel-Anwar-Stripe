<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentIntentController extends Controller
{
    public function index()
    {
        $amount = Cart::session()->first()->courses->sum('price');

        $paymentIntent = Auth::user()->pay($amount);

        return view('checkout.payment-intent', compact('paymentIntent'));
    }

    public function post(Request $request)
    {
        $cart = Cart::session()->first();

        $paymentIntentId = $request->payment_intent_id;
        $paymentIntent = Auth::user()->findPayment($paymentIntentId);

        if ($paymentIntent->status === 'succeeded') {
            $order = Order::create([
                'user_id' => Auth::user()->id,
            ]);

            $order->courses()->attach($cart->courses->pluck('id')->toArray());
            $cart->delete();

            return redirect()->route('home', ['message' => 'Payment Successfull']);
        }
    }
}
