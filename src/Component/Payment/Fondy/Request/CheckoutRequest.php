<?php

namespace App\Component\Payment\Fondy\Request;

/**
 * Class CheckoutRequest
 *
 * @package App\Component\Payment\Fondy\Request
 */
class CheckoutRequest extends RequestAbstract
{
    protected const ACTION = '/api/status/order_id/';
}
