<?php

namespace App\Http\Controllers\Category;

use App\Models\Category;
use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
use App\Http\Controllers\ApiController;

// class CategoryController extends Controller
class CategoryController extends ApiController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();

        return $this->showAll($categories);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => ['required'],
            'description' => ['required'],
        ];

        // request()->validate($rules);
        $request->validate($rules);
        // $validatedData = $request->validate($rules);

        // dd($request->all());
        // dd($validatedData);

        $category = Category::create($request->all());
        // $category = Category::create($validatedData);

        return $this->showOne($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $this->showOne($category);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        // $category->fill($request->intersect([
        $category->fill($request->only([ // fill asigna datos a los atributos del modelo en forma masiva - only en Laravel 5.5 o superior
            'name',
            'description',
        ]));

        
        if ($category->isClean()) { // isClean: Verifica que la instancia no haya cambiado
            return $this->errorResponse('Debe especificar al menos un valor diferente para actualizar', 422);
        }

        $category->save();

        return $this->showOne($category);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return $this->showOne($category);
    }
}
