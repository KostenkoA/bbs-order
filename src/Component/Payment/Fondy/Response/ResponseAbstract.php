<?php

namespace App\Component\Payment\Fondy\Response;

abstract class ResponseAbstract
{
    public const STATUS_SUCCESS = 'success';

    public const STATUS_FAILURE = 'failure';

    /**
     * @var string
     */
    public $response_status;

    /**
     * @var integer
     */
    public $error_code;

    /**
     * @var string
     */
    public $error_message;
}
