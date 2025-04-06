<?php

namespace App\Models;

use Laravel\Cashier\Cashier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function carts()
    {
        return $this->belongsToMany(Cart::class);
    }

    public function price()
    {
        return Cashier::formatAmount($this->price, env('CASHIER_CURRENCY'));
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class);
    }
}
