<?php

namespace App\Component\Payment\Fondy\Request;

/**
 * Class PaymentRequest
 *
 * @package App\Component\Payment\Fondy\Request
 */
class PaymentRequest extends RequestAbstract
{
    protected const ACTION = '/api/checkout/url/';
}
