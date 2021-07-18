<?php

namespace App\Component\UserService\Response;

class UserServiceError
{
    /**
     * @var int
     */
    public $httpCode;

    /**
     * @var int
     */
    public $code;

    /**
     * @var string
     */
    public $error;

    /**
     * @var array;
     */
    public $errors;
}
