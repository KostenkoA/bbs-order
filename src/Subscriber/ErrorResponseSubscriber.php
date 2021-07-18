<?php

namespace App\Subscriber;

use App\Exception\AvailableErrorsExceptionInterface;
use App\Exception\AvailableJsonExceptionInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ErrorResponseSubscriber implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * ErrorResponseSubscriber constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event): void
    {
        $exception = $event->getException();

        $toJson = $this->container->getParameter('kernel.environment') !== 'dev' ||
            $exception instanceof AvailableJsonExceptionInterface;

        if ($toJson) {
            $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $httpCode = Response::HTTP_INTERNAL_SERVER_ERROR;
            $errors = [];

            if ($exception instanceof AvailableJsonExceptionInterface) {
                $statusCode = $exception->getStatusCode();
                $httpCode = $exception->getHttpError();
            }

            if ($exception instanceof AvailableErrorsExceptionInterface) {
                $errors = $exception->getErrors();
            }

            $response = [
                'code' => $statusCode,
                'error' => $exception->getMessage(),
                'errors' => $errors,
            ];
            $event->setResponse(new JsonResponse($response, $httpCode));
        }
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }
}
