<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{
    public function index()
    {
        return view('cart.index');
    }

    public function addToCart(Course $course)

    {
        $cart = Cart::firstOrCreate([
            "session_id" => session()->getId(),
            // if the session id belongs to a user, we will store the user id
            // if not we will store null, and the session id belongs to a guest
            "user_id" => auth()->id() ? auth()->id() : null,
        ]);

        // dd($cart);

        $cart->courses()->syncWithoutDetaching($course);

        return back();
    }

    public function removeFromCart(Course $course)
    {
        // the $course parameter is passed from the home page
        // when the user clicks on the remove button
        $cart = Cart::session()->first();

        // detaching means delete the record of the cart_id and the coures_id from the carts_courses table
        // the cart_id comes from the $cart variable
        // the course_id comes from the $course parameter
        if ($cart) {
            $cart->courses()->detach($course);
        }

        return back();
    }
}
