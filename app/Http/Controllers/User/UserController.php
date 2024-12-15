<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usuarios = User::all();
        // $usuarios = User::get();

        // return $usuarios;
        // return response()->json($usuarios, 200);
        return response()->json(['data' => $usuarios], 200);

        // $headers = [
        //     'Content-Type' => 'application/json; charset=utf-8',
        // ];
        // return response()->json($usuarios, 200, $headers);
        // 
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // $reglas = [
        //     'name' => 'required',
        //     'email' => 'required|email|unique:users',
        //     'password' => 'required|min:6|confirmed',
        // ];

        $reglas = [
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6', 'confirmed'],
        ];

        // $this->validate($request, $reglas); // En Laravel 10<
        // request()->validate($reglas);
        $request->validate($reglas);

        $campos = $request->all();
        // dd($campos);
        // $campos['password'] = bcrypt($request->password);
        $campos['password'] = Hash::make($request->password);
        $campos['verified'] = User::USUARIO_NO_VERIFICADO;
        $campos['verification_token'] = User::generarVerificationToken();
        $campos['admin'] = User::USUARIO_REGULAR;

        $usuario = User::create($campos);

        // // 2da Forma
        // $usuario = User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => bcrypt($request->password),
        //     'password_confirmation' => $request->password,
        //     'verified' => User::USUARIO_NO_VERIFICADO,
        //     'verification_token' => User::generarVerificationToken(),
        //     'admin' => User::USUARIO_REGULAR,
        // ]);

        return response()->json(['data' => $usuario], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // $usuario = User::find($id);
        $usuario = User::findOrFail($id);

        return response()->json(['data' => $usuario], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
