<?php

namespace App\Traits;

use App\Entity\Order;

/**
 * Class OrderPropertyTrait
 * @package App\Traits
 */
trait OrderPropertyTrait
{
    /**
     * @var Order
     */
    protected $order;

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }
}
