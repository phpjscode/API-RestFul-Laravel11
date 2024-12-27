<?php

namespace App\Http\Controllers\Category;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class CategorySellerController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    // public function index(string $id)
    public function index(Category $category)
    {
        // Obtener la lista de los vendedores de una categoría
        // $category = Category::findOrFail($id);
        // $sellers = $category->products()->with('seller')->get(); //Se obtiene una lista de productos, cada una de ellas en su interior con un vendedor
        // 
        $sellers = $category->products()
            ->with('seller')
            ->get()
            ->pluck('seller') // Obtiene solo una parte de esa collection completa en este caso seller (obtener ese indice seller)
            ->unique('id') // Los valores incluidos en la colección sean unicos de acuerdo al id
            ->values(); //Reorganizar los indices en el orden correcto eliminando aquellos que estan vacios

        // dd($sellers);

        return $this->showAll($sellers);
    }
}
