<?php

namespace App\Exceptions;

use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\PostTooLargeException;
use Illuminate\Queue\InvalidPayloadException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    {
        $log = true;
//        if ($exception instanceof OAuthServerException && $exception->getCode() == 9) {
//            $log = false;
//        }

        if ($exception instanceof NotFoundHttpException) {
            $log = false;
        }

        if ($exception instanceof ValidationException) {
            $log = false;
        }

        if ($log) {
            Log::error($exception->getMessage(), ['exception' => $exception]);
        }

        if ($exception instanceof PostTooLargeException) {
            $response = [
                'message' => 'File too large',
            ];
            return response()->json($response, 413);
        }

        if ($exception instanceof NotFoundHttpException) {

            $response = [
                'message' => 'Url not found',
            ];
            if ($request->expectsJson()) {
                return response()->json($response, 404);
            } else {
                return redirect()->route('home');
            }
        }

        if ($exception instanceof ValidationException) {
            $errors = $exception->validator->errors();
            $messages = [];
            foreach ($errors->messages() as $key => $message) {
                $messages[$key] = $message[0];
            }
            $response = [
                'errors' => $messages,
            ];
            return response()->json($response, 422);
        }

        if ($exception instanceof InvalidPayloadException) {
            $response = [
                'message' => $exception->getMessage(),
            ];
            return response()->json($response, 400);
        }

        if ($exception instanceof ModelNotFoundException) {
//            $model = strtolower(class_basename($exception->getModel()));

            $message = "No records found with given data.";
            $response = [
                'message' => $message,
            ];
            return response()->json($response, 404);
        }

        if ($exception instanceof InvalidFormatException) {
            $response = [
                'message' => "Invalid format",
            ];
            return response()->json($response, 400);
        }
        if (config('app.debug')) {
            return parent::render($request, $exception);
        }
        return parent::render($request, $exception);
    }
}
