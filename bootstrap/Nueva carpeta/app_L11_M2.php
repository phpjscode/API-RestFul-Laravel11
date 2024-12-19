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

class ExceptionHandlerWithApiResponser
{
    use ApiResponser;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }   

    public function handleException(Throwable $e, $request)
    {
        // dd($e);
        // 
        if ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }

        if ($e instanceof NotFoundHttpException) {
            // dd($e);
            $message = $e->getMessage();
            $statusCode = $e->getStatusCode();
            // dd($e->getPrevious());
            $previous = $e->getPrevious();
            if ($previous instanceof ModelNotFoundException) {
                // dd($previous->getModel());
                // dd(class_basename($previous->getModel()));
                $message = $previous->getMessage();
                $modelo = strtolower(class_basename($previous->getModel()));
                // dd($message);
                // return $this->errorResponse($message, 404);
                return $this->errorResponse("No existe ninguna instancia de {$modelo} con el id especificado.", 404);
                
            }

            return $this->errorResponse("No se encontró la URL especificada.", 404);
        }

        if ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        }

        if ($e instanceof AuthorizationException) {
            $message = $e->getMessage();
            return $this->errorResponse('No posee permisos para ejecutar esta acción.', 403);
        }

        // if ($e instanceof NotFoundHttpException) {
        //     $statusCode = $e->getStatusCode();
        //     return $this->errorResponse("No se encontró la URL especificada.", 404);
        // } 
        // 
        if ($e instanceof MethodNotAllowedHttpException) {
            $statusCode = $e->getStatusCode();
            return $this->errorResponse('El método especificado en la petición no es válido', 405);
        }

        if ($e instanceof HttpException) {
            return $this->errorResponse($e->getMessage(),  $e->getStatusCode());
        }
        
        if ($e instanceof QueryException) {
            $code = $e->getCode();
            $message = $e->getMessage();

            $codigo = $e->errorInfo[1];
            if ($codigo == 1451) {
                return $this->errorResponse('No se puede eliminar de forma permanente el recurso porque está relacionado con algún otro.', 409);
            } elseif ($codigo == 2002) {
                return $this->errorResponse('No se puede conectar con la base de datos.', 500);
            } else {
                return $this->errorResponse($message, 500);
            }
        }

        if (config('app.debug')) {
           return false; 
        }

        return $this->errorResponse('Falla inesperada. Intente luego.', 500);
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        $message = $e->getMessage();
        return $this->errorResponse('No autenticado.', 401);
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        // $errors = $e->validator->errors()->getMessages();
        // dd($errors);

        $message = $e->getMessage();
        $errors = $e->errors();
        $status = $e->status;

        // return $this->errorResponse($message, 422);
        return $this->errorResponse($errors, 422);
        // return $this->errorResponse($errors, $status);
    }
}

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        // api: __DIR__.'/../routes/api.php',
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
        // $exceptions->render(function (ValidationException $e, Request $request) {
        $exceptions->render(function (Throwable $e, Request $request) {
            // dd($e);
            $handler = new ExceptionHandlerWithApiResponser();
            return $handler->handleException($e, $request);
            // if ($e instanceof ValidationException) {
            //     $message = $e->getMessage();
            //     $errors = $e->errors();
            //     $status = $e->status;

            //     return response()->json(['error' => $errors, 'code ' => 422], 422);
            // }
        });
    })->create();
