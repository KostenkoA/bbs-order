<?php

namespace App\Service;

use App\Component\ESputnik\Builder\OrderBuilder;
use App\Component\ESputnik\ESputnikActionException;
use App\Component\ESputnik\ESputnikComponent;
use App\Component\ESputnik\Response\ESputnikError;
use App\Entity\Order;
use GuzzleHttp\Exception\GuzzleException;

class ESputnikService
{
    /**
     * @var ESputnikComponent
     */
    private $eSputnikComponent;

    /**
     * @var OrderBuilder
     */
    private $orderBuilder;

    public function __construct(ESputnikComponent $component, OrderBuilder $builder)
    {
        $this->eSputnikComponent = $component;
        $this->orderBuilder = $builder;
    }

    /**
     * @param Order $order
     * @return mixed
     * @throws ESputnikActionException
     * @throws ESputnikError
     * @throws GuzzleException
     */
    public function sendOrder(Order $order)
    {
        return $this->eSputnikComponent->sendOrders($this->orderBuilder->buildOrderESputnikDTO($order));
    }
}
