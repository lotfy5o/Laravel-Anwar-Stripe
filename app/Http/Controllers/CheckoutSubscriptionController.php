<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutSubscriptionController extends Controller
{
    public function index(Plan $plan)
    {
        return Auth::user()->newSubscription($plan->slug, $plan->stripe_price_id)->checkout([
            'success_url' => route('home', ['status' => 'Subscriped Successfully']),
            'cancel_url' => route('plans'),
        ]);
    }

    // this functin if I am using a trails days, but something is not working 
    // public function index(Plan $plan)
    // {
    //     $query = Auth::user()->newSubscription($plan->slug, $plan->stripe_price_id);
    //     return $query->when($plan->slug == 'monthly-plan', function () use ($query) {
    //         return $query->trialDays(7);
    //     })->checkout([
    //         'success_url' => route('home', ['status' => 'Subscriped Successfully']),
    //         'cancel_url' => route('plans'),
    //     ]);
    // }


    public function indexDirect(Plan $plan)
    {
        return view('checkout.payment-method', get_defined_vars());
    }

    public function post(Request $request)
    {

        $plan = Plan::findOrFail($request->plan_id);

        if ($plan->slug == 'lifetime-membership') {
            return $this->CheckoutLifetimeMembershipDirectIntegraion($plan, $request);
            // return $this->CheckoutLifetimeMembership();
        }

        $subscription = Auth::user()->newSubscription($plan->slug, $plan->stripe_price_id)
            // ->quantity(10)
            ->create($request->payment_method);

        if ($subscription->stripe_status == 'active') {
            return redirect()->route('home', ['status' => 'Subscriped Successfully']);
        } else {
            return redirect()->route('home')->with('status', 'Subscription Failed');
        }
    }

    public function CheckoutLifetimeMembership()
    {
        // مشكلة الدالة ان مفيش ويب هوك فالريكورد مش هيتكريت لإني عندي مشكلة في دالة
        // newSubscription 
        // طريقة الهوستيد مش شغالة مش عارف ليه.
        // تقريبا عشان انا مش مشغل الويب هوك فالريكورد بيتكريت في سترايب ومبيتكريتش في الداتابيز

        $price = Plan::firstWhere('slug', 'lifetime-membership')->stripe_price_id;
        return Auth::user()->checkout($price, [
            'success_url' => route('home', ['message', 'Subscribed Successfully']),
            'cancel_url' => route('plans')
        ]);
    }
    public function CheckoutLifetimeMembershipDirectIntegraion(Plan $plan, Request $request)
    {

        $payment = Auth::user()->charge($plan->price, $request->payment_method, [
            'return_url' => route('home')
        ]);

        if ($payment->status == 'succeeded') {
            Auth::user()->update(['lifetime_membership' => true]);

            return redirect()->route('home', ['status' => 'Subscriped Successfully']);
        }
    }
}
