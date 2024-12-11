<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\HasMany;

// use Illuminate\Database\Eloquent\Model;

class Seller extends User
{
    public function hasMany(): HasMany
    {
        return $this->hasMany(Product::class); // Un vendedor tiene muchos productos
    }
}
