<?php

namespace App\Http\Controllers\Buyer;

use App\Models\Buyer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BuyerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $compradores = Buyer::all(); // Listará todos los usuarios
        $compradores = Buyer::has('transactions')->get(); // Todos los usuarios que tengan transacciones - has recibe el nombre de una relación de Buyer

        // dd($compradores);

        return response()->json(['data' => $compradores], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
}
