<?php

namespace App\Exception;

use Symfony\Component\HttpFoundation\Response;

class UserRefOrderException extends ObjectNotFoundException
{
    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return '1001';
    }
}
