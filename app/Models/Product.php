<?php

namespace App\Models;

use App\Models\Seller;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    const PRODUCTO_DISPONIBLE = 'disponible';
    const PRODUCTO_NO_DISPONIBLE = 'no disponible';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'quantity',
        'status',
        'image',
        'seller_id',
    ];

    public function estaDisponible()
    {
        return $this->status == Product::PRODUCTO_DISPONIBLE;
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class); // Un producto pertenece a muchos categories
    }

    public function seller(): BelongsTo 
    {
        // return $this->belongsTo('App\Models\Seller'); // Un producto pertenece a un seller
        return $this->belongsTo(Seller::class); // Un producto pertenece a un seller
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class); // Un producto tiene muchas transacciones
    }

}
