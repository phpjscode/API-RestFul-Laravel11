<?php

namespace App\Http\Controllers\Buyer;

use App\Models\Buyer;
use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

// class BuyerController extends Controller
class BuyerController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $compradores = Buyer::all(); // Listará todos los usuarios
        // $compradores = Buyer::has('transactions')->get(); // Todos los usuarios que tengan transacciones - has recibe el nombre de una relación de Buyer

        // $compradores = Buyer::hasTransactions()->get(); // Hacemos uso del local Scope para todos los usuarios que tengan transacciones - has recibe el nombre de una relación de Buyer
        
        // $compradores = Buyer::hasTransactions()->idAscending()->get(); // Hacemos uso del local Scope para todos los usuarios que tengan transacciones - has recibe el nombre de una relación de Buyer

         $compradores = Buyer::all(); // Listará todos los buyer haciendo uso del global scope 
        // $compradores = Buyer::orderBy('id')->get(); // Listará todos los buyer haciendo uso del global scope 

        // return response()->json(['data' => $compradores], 200);
        return $this->showAll($compradores);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $comprador = Buyer::has('transactions')->findOrFail($id);
        // 
        // $comprador = Buyer::hasTransactions()->findOrFail($id); // Hacemos uso del local Scope
        // 
        $comprador = Buyer::findOrFail($id); // Hacemos uso del global Scope

        // return response()->json(['data' => $comprador], 200);
        return $this->showOne($comprador);
    }
}
