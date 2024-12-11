<?php

namespace App\Models;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Relations\HasMany;

// use Illuminate\Database\Eloquent\Model;

class Buyer extends User
{
    public function transactions(): HasMany
    {
        // return $this->hasMany('App\Models\Transaction'); // Un comprador tiene muchas transacciones
        // return $this->hasMany(Transaction::class, 'buyer_id'); // Un comprador tiene muchas transacciones
        return $this->hasMany(Transaction::class); // Un comprador tiene muchas transacciones
    }
}
