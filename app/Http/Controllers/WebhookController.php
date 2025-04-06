<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

class WebhookController extends CashierWebhookController
{


    protected function handleChargeSucceeded(array $payload)
    {
        Log::info('hello from webhook controller');
    }
}
