<?php

namespace App\Event;

use App\DTO\OneC\OrderStatusError1C;
use App\Entity\Order;
use App\Interfaces\OrderEventInterface;
use App\Traits\OrderPropertyTrait;
use Symfony\Component\EventDispatcher\Event;

class OrderErrorEvent extends Event implements OrderEventInterface
{
    use OrderPropertyTrait;

    public const EVENT_STATUS_ERROR = 'app.order.status-error';

    /**
     * @var \App\DTO\OneC\OrderStatusError1C
     */
    protected $orderError;

    /**
     * OrderErrorEvent constructor.
     * @param Order $order
     * @param OrderStatusError1C $orderError
     */
    public function __construct(Order $order, OrderStatusError1C $orderError)
    {
        $this->order = $order;
        $this->orderError = $orderError;
    }

    /**
     * @return \App\DTO\OneC\OrderStatusError1C
     */
    public function getOrderError(): OrderStatusError1C
    {
        return $this->orderError;
    }
}
