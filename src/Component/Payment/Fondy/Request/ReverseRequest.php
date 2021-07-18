<?php

namespace App\Component\Payment\Fondy\Request;

class ReverseRequest extends RequestAbstract
{
    protected const ACTION = '/api/reverse/order_id/';
}
