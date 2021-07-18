<?php

namespace App\Component\Delivery\Response;

class DeliverySearchError
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
