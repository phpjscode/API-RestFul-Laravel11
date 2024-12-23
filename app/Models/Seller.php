<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Scopes\SellerScope;
use Illuminate\Database\Eloquent\Relations\HasMany;

// use Illuminate\Database\Eloquent\Model;

class Seller extends User
{
    /**
     * Bootstrap the model and its traits.
     *
     * @return void
     */
    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new SellerScope);
    // }
    
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        // parent::booted();
        static::addGlobalScope(new SellerScope);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class); // Un vendedor tiene muchos productos
    }
}
