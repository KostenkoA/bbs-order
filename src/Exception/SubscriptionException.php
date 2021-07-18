<?php

namespace App\Exception;

use Exception;

class SubscriptionException extends Exception implements AvailableJsonExceptionInterface
{
    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return '2001';
    }

    /**
     * @return int
     */
    public function getHttpError(): int
    {
        return $this->getCode();
    }
}
