<?php

namespace App\Service\Order;

use App\Builder\OrderBuilder;
use App\Entity\Order;
use App\Interfaces\OrderSend1CInterface;

class Order1CService
{
    /**
     * @var OrderSend1CInterface
     */
    private $orderSend1C;

    /**
     * @var OrderBuilder
     */
    private $modelBuilder;

    /**
     * Order1CService constructor.
     * @param OrderSend1CInterface $orderSend1C
     * @param OrderBuilder $modelBuilder
     */
    public function __construct(
        OrderSend1CInterface $orderSend1C,
        OrderBuilder $modelBuilder
    ) {
        $this->orderSend1C = $orderSend1C;
        $this->modelBuilder = $modelBuilder;
    }

    /**
     * @param Order $order
     */
    public function sendTo1c(Order $order): void
    {
        $this->orderSend1C->sendNewOrder($this->modelBuilder->build1CDTO($order));
    }
}
