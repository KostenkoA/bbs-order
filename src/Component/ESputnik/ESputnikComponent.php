<?php

namespace App\Component\ESputnik;

use App\Component\ESputnik\Action\SendOrdersAction;
use App\DTO\ESputnik\OrdersDTO;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Container\ContainerInterface;

class ESputnikComponent
{
    /**
     * @var ContainerInterface
     */
    private $actionLocator;

    /**
     * ProductComponent constructor.
     * @param ContainerInterface $actionLocator
     */
    public function __construct(ContainerInterface $actionLocator)
    {
        $this->actionLocator = $actionLocator;
    }

    /**
     * @param $class
     * @return ESputnikActionInterface
     * @throws ESputnikActionException
     */
    private function getAction($class): ESputnikActionInterface
    {
        if (!$this->actionLocator->has($class)) {
            throw new ESputnikActionException('Can\'t found command by class ' . $class);
        }
        /** @var ESputnikActionInterface $action */
        $action = $this->actionLocator->get($class);

        return $action;
    }

    /**
     * @param OrdersDTO $ordersDTO
     * @return mixed
     * @throws ESputnikActionException
     * @throws Response\ESputnikError
     * @throws GuzzleException
     */
    public function sendOrders(OrdersDTO $ordersDTO)
    {
        /** @var SendOrdersAction $action */
        $action = $this->getAction(SendOrdersAction::class);

        return $action->sendAction($ordersDTO);
    }
}
