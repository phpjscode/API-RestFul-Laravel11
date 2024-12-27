<?php

namespace App\Http\Controllers\Buyer;

use App\Models\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerTransactionController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    // public function index(string $id)
    public function index(Buyer $buyer)
    {
        // Obtener la lista de transacciones de un comprador
        // 1RA FORMA
        // $buyer = Buyer::findOrFail($id);
        // $transactions = $buyer->transactions;
        // 
        // 2DA FORMA
        $transactions = $buyer->transactions;
        
        // dd($transactions);
        return $this->showAll($transactions);
    }
}
