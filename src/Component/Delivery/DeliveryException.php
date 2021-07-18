<?php

namespace App\Component\Delivery;

use App\Exception\AvailableJsonExceptionInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class DeliveryException extends Exception implements AvailableJsonExceptionInterface
{
    /**
     * @return int
     */
    public function getHttpError(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return (int)$this->getCode();
    }
}
