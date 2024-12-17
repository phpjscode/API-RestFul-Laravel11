<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
// use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\ApiController;

// class UserController extends Controller
class UserController extends ApiController
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
        $campos['email_verified_at'] = null; // Fecha y hora en que el email fue verificado.
        $campos['verified'] = User::USUARIO_NO_VERIFICADO;
        $campos['verification_token'] = User::generarVerificationToken(); // Código de verificación de electrónico
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
        $user = User::findOrFail($id);
        // dd($user);

        // dd($user, request()->all(), $request->all());
        // 
        // $reglas = [
        //     // 'email' => 'email|unique:users,email,' . $user->id,
        //     'email' => ['email', Rule::unique('users')->ignore($user->id)],
        //     'password' => 'min:6|confirmed',
        //     'admin' => 'in:' . User::USUARIO_ADMINISTRADOR . ',' . User::USUARIO_REGULAR,
        // ];

        $reglas = [
            'email' => ['email', 'unique:users,email,' . $user->id],
            // 'email' => ['email', Rule::unique('users')->ignore($user->id)],
            // 'email' => ['email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['min:6', 'confirmed'],
            'admin' => ['in:' . User::USUARIO_ADMINISTRADOR . ',' . User::USUARIO_REGULAR],
        ];

        // request()->validate($reglas);
        $request->validate($reglas);

        // dd(request()->all(), $request->all(), $user, $request->validate($reglas));
        // 
        if ($request->has('name')) { // Si la petición tiene un campo name
            $user->name = $request->name;
            // $user->name = request()->name;
            // $user->name = request()->input('name');
        }

        if ($request->has('email') && $user->email != $request->email) {
            $user->email_verified_at = null; // Fecha y hora en que el email fue verificado.
            $user->verified = User::USUARIO_NO_VERIFICADO; // Obs: Si es 1 y se cambia a 0 entonces en $user->esVerificado() devuelve Falso
            $user->verification_token = User::generarVerificationToken();
            $user->email = $request->email;
        }

        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
            // $user->password = Hash::make($request->password);
        }

        if ($request->has('admin')) {
            if (!$user->esVerificado()) {
                return response()->json(['error' => 'Unicamente los usuarios verificados pueden cambiar su valor de administrador.', 'code' => 409], 409);
            }
            $user->admin = $request->admin;
        }

        if (!$user->isDirty()) { // isDirty determina si alguno de los atributos del modelo ha cambiado respecto al valor actual
            return response()->json(['error' => 'Se debe especificar al menos un valor diferente para actualizar.', 'code' => 422], 422);
        }

        $user->save();

        return response()->json(['data' => $user], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json(['data' => $user], 200);
    }
}
