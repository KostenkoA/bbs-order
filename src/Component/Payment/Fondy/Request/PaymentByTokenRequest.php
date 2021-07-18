<?php

namespace App\Component\Payment\Fondy\Request;

/**
 * Class PaymentRequest
 *
 * @package App\Component\Payment\Fondy\Request
 */
class PaymentByTokenRequest extends RequestAbstract
{
    protected const ACTION = '/api/recurring/';
}
