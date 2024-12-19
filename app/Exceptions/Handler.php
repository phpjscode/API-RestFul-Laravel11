<?php

namespace App\Exceptions;

use Throwable;
use App\Traits\ApiResponser;
use Illuminate\Database\QueryException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    use ApiResponser;

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $e
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        // dd($e);

        if ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }

        // if ($e instanceof NotFoundHttpException) {
        //     $message = $e->getMessage();
        //     $statusCode = $e->getStatusCode();
        //     $previous = $e->getPrevious();
        //     if ($previous instanceof ModelNotFoundException) {
        //         $message = $previous->getMessage();
        //         $modelo = strtolower(class_basename($previous->getModel()));
        //         return $this->errorResponse("No existe ninguna instancia de {$modelo} con el id especificado.", 404);
                
        //     }

        //     return $this->errorResponse("No se encontró la URL especificada.", 404);
        // }
        
        if ($e instanceof ModelNotFoundException) {
            $message = $e->getMessage();
            $modelo = strtolower(class_basename($e->getModel()));
            return $this->errorResponse("No existe ninguna instancia de {$modelo} con el id especificado.", 404);
        }

        if ($e instanceof AuthenticationException) {
            return $this->unauthenticated($request, $e);
        }

        if ($e instanceof AuthorizationException) {
            $message = $e->getMessage();
            return $this->errorResponse('No posee permisos para ejecutar esta acción.', 403);
        }

        if ($e instanceof NotFoundHttpException) {
            $statusCode = $e->getStatusCode();
            return $this->errorResponse("No se encontró la URL especificada.", 404);
        } 
        
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
           return parent::render($request, $e);
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
        $message = $e->getMessage();
        $errors = $e->errors();
        $status = $e->status;

        // return $this->errorResponse($message, 422);
        return $this->errorResponse($errors, 422);
        // return $this->errorResponse($errors, $status);
    }
}
