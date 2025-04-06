<?php

namespace App\Console\Commands;

use App\Models\Plan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class StripePlansCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stripe:plans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Stripe Plans from Stripe API';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        // problem
        // when I run this command all the interval in the database turns into "once"
        // not just the lifetime membership record

        $stripe = new \Stripe\StripeClient('sk_test_51R4J0iQOOQD5fiJ8wvhel7hlyxLvmSGdpODtZ5cMy4UMvUHeaKdSZ1epHzwsjzQ4gI8oEzJ9vlOZ9QOQo1GKbCiw00p8ftgwqC');

        $plans = $stripe->products->all();

        foreach ($plans->data as $plan) {
            // log::info($plan);

            $price = $stripe->prices->retrieve($plan->default_price, []);
            Plan::updateOrCreate(['name' => $plan->name], [
                'price' => $price->unit_amount,
                'stripe_price_id' => $price->id,
                'interval' => $price->recurring->interval,
            ]);
        }
    }
}
