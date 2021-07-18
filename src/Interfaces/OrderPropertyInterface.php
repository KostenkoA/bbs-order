<?php

namespace App\Interfaces;

use App\Entity\Order;

interface OrderPropertyInterface
{
    /**
     * @return Order
     */
    public function getOrder(): Order;
}
