<?php

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        using: function () {
            // Route::middleware('api')
            //     ->prefix('api')
            //     ->group(base_path('routes/api.php'));

            Route::middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        },
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
        $exceptions->render(function (Throwable $e, Request $request) {
            // dd($e);
            // Clase anónima - Hacer que el método errorResponse sea público en el Trait
            $responser = new class {
                use ApiResponser;
            };

            if ($e instanceof ValidationException) {
                $errors = $e->errors();
                // return response()->json(['error' => $errors, 'code' => 422], 422);
                return $responser->errorResponse($errors, 422);
            }

            if ($e instanceof NotFoundHttpException) {
                $message = $e->getMessage();
                $statusCode = $e->getStatusCode();
                $previous = $e->getPrevious();
                if ($previous instanceof ModelNotFoundException) {
                    $message = $previous->getMessage();
                    $modelo = strtolower(class_basename($previous->getModel()));
                    return $responser->errorResponse("No existe ninguna instancia de {$modelo} con el id especificado.", 404);
                    
                }

                return $responser->errorResponse("No se encontró la URL especificada.", 404);
            }

            if ($e instanceof AuthenticationException) {
                return $responser->errorResponse('No autenticado.', 401);
            }

            if ($e instanceof AuthorizationException) {
                $message = $e->getMessage();
                return $responser->errorResponse('No posee permisos para ejecutar esta acción.', 403);
            }

            // if ($e instanceof NotFoundHttpException) {
            //     $statusCode = $e->getStatusCode();
            //     return $responser->errorResponse("No se encontró la URL especificada.", 404);
            // } 
            // 
            if ($e instanceof MethodNotAllowedHttpException) {
                $statusCode = $e->getStatusCode();
                return $responser->errorResponse('El método especificado en la petición no es válido', 405);
            }

            if ($e instanceof HttpException) {
                return $responser->errorResponse($e->getMessage(),  $e->getStatusCode());
            }
            
            if ($e instanceof QueryException) {
                $code = $e->getCode();
                $message = $e->getMessage();

                $codigo = $e->errorInfo[1];
                if ($codigo == 1451) {
                    return $responser->errorResponse('No se puede eliminar de forma permanente el recurso porque está relacionado con algún otro.', 409);
                } elseif ($codigo == 2002) {
                    return $responser->errorResponse('No se puede conectar con la base de datos.', 500);
                } else {
                    return $responser->errorResponse($message, 500);
                }
            }

            if (config('app.debug')) {
               return false; 
            }

            return $responser->errorResponse('Falla inesperada. Intente luego.', 500);

        });
    })->create();
