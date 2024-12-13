<?php

namespace App\Models;

use App\Models\Buyer;
use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'quantity',
        'buyer_id',
        'product_id',
    ];

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);  // Una transacción pertenece a un comprador
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class); // Una transacción pertenece a un product
    }
}
