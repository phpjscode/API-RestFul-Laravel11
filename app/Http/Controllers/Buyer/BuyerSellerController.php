<?php

namespace App\Http\Controllers\Buyer;

use App\Models\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerSellerController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    // public function index(string $id)
    public function index(Buyer $buyer)
    {
        // Obtener los vendedores de un comprador específico
        // $buyer = Buyer::findOrFail($id);
        // $sellers = $buyer->transactions()->with('product.seller')->get();
        // 
        $sellers = $buyer->transactions()->with('product.seller')
            ->get()
            ->pluck('product.seller')
            ->unique('id') // Los valores incluidos en la colección sean unicos de acuerdo al id
            ->values(); //Reorganizar los indices en el orden correcto eliminando aquellos que estan vacios

        // dd($sellers);
        return $this->showAll($sellers);
    }
}
