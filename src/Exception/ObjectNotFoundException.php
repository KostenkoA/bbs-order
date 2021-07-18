<?php

namespace App\Exception;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class ObjectNotFoundException extends Exception implements AvailableJsonExceptionInterface
{
    /**
     * @return int
     */
    public function getHttpError(): int
    {
        return Response::HTTP_NOT_FOUND;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return Response::HTTP_NOT_FOUND;
    }
}
