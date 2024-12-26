<?php

namespace App\Http\Controllers\Transaction;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class TransactionSellerController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    // public function index(string $id)
    public function index(Transaction $transaction)
    {
        // Obtener el vendedor de una transacción
        // $transaction = Transaction::findOrFail($id);
        // $seller = $transaction->product->seller;

        $seller = $transaction->product->seller;
        // dd($seller);
        return $this->showOne($seller);
    }
}
