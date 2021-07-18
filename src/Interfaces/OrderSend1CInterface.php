<?php

namespace App\Interfaces;

use App\DTO\OneC\Order1C;

interface OrderSend1CInterface
{
    /**
     * @param Order1C $order
     */
    public function sendNewOrder(Order1C $order): void;
}
