<?php

namespace App\Http\Controllers\Transaction;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

class TransactionCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    // public function index(string $id)
    public function index(Transaction $transaction)
    {
        // Obtener la lista de categorías de una transacción específica - Categorías del producto que esta involucrado en esa transacción
        // 1RA FORMA
        // $categories = Transaction::findOrFail($id)->product->categories;

        // 2DA FORMA
        // $transaction = Transaction::findOrFail($id); 
        // $categories = $transaction->product->categories; 

        // 3RA FORMA
        $categories = $transaction->product->categories;

        // dd($categories);

        return $this->showAll($categories);
    }
}
