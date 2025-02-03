<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $exception)
    {
        // Verificar si la excepción es una excepción de autenticación
        if ($exception instanceof AuthenticationException) {
            // Verificar si la solicitud espera una respuesta JSON
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Credenciales no válidas. Por favor, intenta de nuevo con las credenciales correctas.'
                ], 401); // Código 401 Unauthorized
            }

            // Si la solicitud no es JSON, devolver el comportamiento por defecto (página HTML)
            return response()->view('errors.401', [], 401);
        }

        // Para todas las demás excepciones, devolver la respuesta por defecto
        return parent::render($request, $exception);
    }
}
