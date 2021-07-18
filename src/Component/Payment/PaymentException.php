<?php

namespace App\Component\Payment;

use App\Exception\AvailableJsonExceptionInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class PaymentException extends Exception implements AvailableJsonExceptionInterface
{
    public function getHttpError(): int
    {
        return Response::HTTP_INTERNAL_SERVER_ERROR;
    }


    public function getStatusCode(): int
    {
        return (int)$this->getCode();
    }
}
