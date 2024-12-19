<?php

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: '',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
        $apiResponser = new class {
            use ApiResponser;
        };
        
        // Averiguar el tipo de la excepción que se dispara
        // $exceptions->render(function (Throwable $e, Request $request) {
        //     dd($e);
        // });

        $exceptions->render(function (ValidationException $e, Request $request) use ($apiResponser) {
            // dd($e);
            // $errors = $e->validator->errors()->getMessages();
            // dd($errors);

            $message = $e->getMessage();
            $errors = $e->errors();
            $status = $e->status;

            // return response()->json(['error' => $message, 'code' => 422], 422);
            return $apiResponser->errorResponse($errors, 422);
            // return $apiResponser->errorResponse($errors, $status);
        });

    })->create();
