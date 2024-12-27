<?php

namespace App\Http\Controllers\Buyer;

use App\Models\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerCategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    // public function index(string $id)
    public function index(Buyer $buyer)
    {
        // Obtiene la lista de todas las categorías donde un comprador ha realizado compras
        // $buyer = Buyer::findOrFail($id);
        // $categories = $buyer->transactions()->with('product.categories')->get();

        $categories = $buyer->transactions()->with('product.categories')
            ->get()
            ->pluck('product.categories')
            ->collapse() // Juntar todas las listas de categorías en una sola
            ->unique('id')
            ->values();
        // dd($categories);

        return $this->showAll($categories);
    }
}
