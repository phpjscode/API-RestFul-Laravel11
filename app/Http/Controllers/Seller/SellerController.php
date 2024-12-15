<?php

namespace App\Http\Controllers\Seller;

use App\Models\Seller;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SellerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $vendedores = Seller::has('products')->get();
        $vendedores = Seller::all(); // Listará todos los Seller haciendo uso del global scope 
        // $vendedores = Seller::orderBy('id', 'asc')->get(); // Listará todos los Seller haciendo uso del global scope 

        return response()->json(['data' => $vendedores], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $vendedor = Seller::has('products')->findOrFail($id);
        // 
        $vendedor = Seller::findOrFail($id); // Hacemos uso del global Scope

        return response()->json(['data' => $vendedor], 200);
    }
}
