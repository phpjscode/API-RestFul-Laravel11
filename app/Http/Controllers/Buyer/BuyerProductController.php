<?php

namespace App\Http\Controllers\Buyer;

use App\Models\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class BuyerProductController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    // public function index(string $id)
    public function index(Buyer $buyer)
    {
        // Obtener todos los productos que un comprador ha comprado
        // 1RA FORMA
        // $products = $buyer->transactions->product; //Lo que sucede aquí es que se esta obteniendo una lista de transacciones y Laravel automáticamente lo convierte en una collection, al ser una collection ya deja de ser un transaccion como tal y por ende no se puede acceder de manera directa a los productos de la misma. Para esto Laravel tiene algo conocido como Eager loading
        // 
        // $products = $buyer->transactions()->with('product')->get(); //Se obtiene una lista de transacciones, cada una de ellas en su interior con un producto
        // 
        $products = $buyer->transactions()->with('product')
            ->get()
            ->pluck('product'); // Obtiene solo una parte de esa collection completa en este caso product (obtener ese indice product)

        // // 2DA FORMA
        // $buyer = Buyer::findOrFail($id);
        // $transactions = $buyer->transactions; // Obtener las transacciones como colección
        // $transactions->load('product'); // load() para cargar relaciones - Eager loading de los productos relacionados con cada transacción
        // $products = $transactions->pluck('product'); // Obtener los productos de las transacciones
        
        // dd($products);
        
        return $this->showAll($products);
    }
}
