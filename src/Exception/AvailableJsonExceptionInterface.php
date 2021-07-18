<?php

namespace App\Exception;

interface AvailableJsonExceptionInterface
{
    /**
     * @return int
     */
    public function getHttpError(): int;

    /**
     * @return int
     */
    public function getStatusCode(): int;
}
