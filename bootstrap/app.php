<?php

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        
        // Averiguar el tipo de la excepción que se dispara (Throwable es la clase base de todas las excepciones de PHP Laravel)
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

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($apiResponser) {
            $message = $e->getMessage();
            $statusCode = $e->getStatusCode();
            $previous = $e->getPrevious();
            if ($previous instanceof ModelNotFoundException) {
                $message = $previous->getMessage();
                $modelo = strtolower(class_basename($previous->getModel()));
                $ids = $previous->getIds();
                $id = $ids[0] ?? 'desconocido';
                // dd($id);
                return $apiResponser->errorResponse("No existe ninguna instancia de {$modelo} con el id {$id} especificado.", 404);
                
            }
            return $apiResponser->errorResponse("No se encontró la URL especificada.", 404);
        }); 

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($apiResponser) {
            return $apiResponser->errorResponse('No autenticado.', 401);
        });

    })->create();
