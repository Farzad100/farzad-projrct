<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
  protected $dontReport = [
//
  ];

  protected $dontFlash = [
    'password',
    'password_confirmation',
  ];

  public function report(Throwable $exception)
  {
    parent::report($exception);
  }

  public function render($request, Throwable $exception)
  {
// is this request asks for json?
    if ($request->header('Content-Type') == 'application/json') {

      /*  is this exception? */
      if (!empty($exception)) {
        $message = $exception->getMessage();
// set default error message
        $response = [
          'error' => 'Sorry, can not execute your request.'
        ];

// If debug mode is enabled
        if (config('app.debug')) {
// Add the exception class name, message and stack trace to response
          $response['exception'] = get_class($exception); // Reflection might be better here
          $response['message'] = $exception->getMessage();
          $response['trace'] = $exception->getTrace();
        }

        $status = 400;

// get correct status code

// is this validation exception
        if ($exception instanceof ValidationException) {

          return $this->convertValidationExceptionToResponse($exception, $request);

// is it authentication exception
        } else if ($exception instanceof AuthenticationException) {

          $status = 401;

          $response['error'] = 'Can not finish authentication!';

//is it DB exception
        } else if ($exception instanceof \PDOException) {

          $status = 500;

          $response['error'] = 'Can not finish your query request!';

// is it http exception (this can give us status code)
        } else if ($this->isHttpException($exception)) {

          $status = $exception->getStatusCode();

          $response['error'] = 'Request error!';

        } else {

// for all others check do we have method getStatusCode and try to get it
// otherwise, set the status to 400
          $status = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 400;

        }

        $response['code'] = $status;
        if (!empty($message)) $response['error'] = $message;
        return response()->json($response, $status);

      }
    }

    return parent::render($request, $exception);
  }
}
