<?php

namespace App\EventListener;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionListener
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $code = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : $exception->getCode();
        $response = new JsonResponse([
            'error' => true,
            'code' => $code,
            'message' => $exception->getMessage(),
        ], Response::HTTP_OK);
        $event->allowCustomResponseCode();
        $response->headers->set('X-LM-Header', 'error');
        $event->setResponse($response);
    }
}
