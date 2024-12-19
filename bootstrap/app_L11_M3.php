<?php

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
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
            return $apiResponser->errorResponse($message, 404);
            // return $apiResponser->errorResponse("No se encontró la URL especificada.", 404);
        }); 

        $exceptions->render(function (AuthenticationException $e, Request $request) use ($apiResponser) {
            return $apiResponser->errorResponse('No autenticado.', 401);
        });
        
        $exceptions->render(function (AuthorizationException $e, Request $request) use ($apiResponser) {
            return $apiResponser->errorResponse('No posee permisos para ejecutar esta acción.', 403);
        });

        // $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($apiResponser) {
        //     $message = $e->getMessage();
        //     $statusCode = $e->getStatusCode();

        //     return $apiResponser->errorResponse("No se encontró la URL especificada.", 404);
        // }); 
        // 
        $exceptions->render(function (MethodNotAllowedHttpException $e, Request $request) use ($apiResponser) {
            $message = $e->getMessage();
            $statusCode = $e->getStatusCode();

            return $apiResponser->errorResponse('El método especificado en la petición no es válido', 405);
        });

        $exceptions->render(function (HttpException $e, Request $request) use ($apiResponser) {
            $message = $e->getMessage();
            $statusCode = $e->getStatusCode();

            return $apiResponser->errorResponse($message, $statusCode);
        });

        $exceptions->render(function (QueryException $e, Request $request) use ($apiResponser) {
            $code = $e->getCode();
            $message = $e->getMessage();
            $codigo = $e->errorInfo[1];

            if ($codigo == 1451) {
                return $apiResponser->errorResponse('No se puede eliminar de forma permanente el recurso porque está relacionado con algún otro.', 409);
            } elseif ($codigo == 2002) {
                return $apiResponser->errorResponse('No se puede conectar con la base de datos.', 500);
            } else {
                // return $apiResponser->errorResponse($message, 500);
                return $apiResponser->errorResponse('Falla inesperada. Intente luego.', 500);
            }
        });

        if (config('app.debug')) {
            return false;
        }

        return $apiResponser->errorResponse('Falla inesperada. Intente luego.', 500);


    })->create();
