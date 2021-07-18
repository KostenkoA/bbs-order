<?php

namespace App\Component;

class RequestResponseError
{
    /** @var int */
    public $httpCode;

    /** @var int */
    public $code;

    /** @var string */
    public $error;

    /** @var array; */
    public $errors;
}
