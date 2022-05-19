<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];
    
    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];
    
    /**
     * Report or log an exception.
     *
     * @param Throwable $exception
     *
     * @return void
     *
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }
    
    /**
     * Render an exception into an HTTP response.
     *
     * @param Request   $request
     * @param Throwable $exception
     *
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return $request->wantsJson() ?
                response()->json($this->transformErrors($exception), $exception->status) :
                $this->invalid($request, $exception);
        }
        
        return parent::render($request, $exception);
    }
    
    /**
     * @param ValidationException $exception
     *
     * @return array
     */
    protected function transformErrors(ValidationException $exception): array
    {
        $output = [];
        foreach ($exception->validator->getMessageBag()->getMessages() as $key => $messages) {
            $output[] = [
                'property_path' => $this->getPropertyPath($key, $exception->validator->customAttributes),
                'message'       => $messages[0],
            ];
        }
        
        return $output;
    }
    
    /**
     * @param string $key
     * @param array  $customAttributes
     *
     * @return string
     */
    private function getPropertyPath(string $key, array $customAttributes): string
    {
        if (isset($customAttributes[$key])) {
            return $customAttributes[$key];
        }
        
        if (str_contains($key, '_')) {
            return str_replace('_', ' ', $key);
        }
        
        return $key;
    }
    
    /**
     * @param Request   $request
     * @param Throwable $e
     *
     * @return JsonResponse
     */
    protected function prepareJsonResponse($request, Throwable $e)
    {
        return new JsonResponse(
            [
                [
                    'property_path' => 'general',
                    'message'       => $e->getMessage(),
                ],
            ],
            $this->isHttpException($e) ? $e->getStatusCode() : 500,
            $this->isHttpException($e) ? $e->getHeaders() : [],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }
    
    
}
