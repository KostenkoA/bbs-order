<?php

namespace App\Component\Product;

use App\Exception\AvailableJsonExceptionInterface;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ProductSearchException extends Exception implements AvailableJsonExceptionInterface
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
