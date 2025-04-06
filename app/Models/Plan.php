<?php

namespace App\Models;

use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Cashier\Cashier;

class Plan extends Model
{
    use HasFactory;
    use HasSlug;

    protected $table = 'plans';
    protected $fillable = ['name', 'slug', 'price', 'stripe_price_id', 'interval'];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name') // Field to generate the slug from
            ->saveSlugsTo('slug');     // Field to save the slug to
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }


    public function price()
    {
        return Cashier::formatAmount($this->price, env('STRIPE_CURRENCY'), 'usd');
    }
}
