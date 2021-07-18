<?php

namespace App\EventListener;

use App\Entity\Order;
use App\Event\OrderEvent;
use App\Service\Order\Order1CService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Order1CListener
{
    /**
     * @var Order1CService
     */
    protected $order1CService;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Order1CListener constructor.
     * @param Order1CService $order1CService
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManagerInterface $em
     */
    public function __construct(
        Order1CService $order1CService,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $em
    ) {
        $this->order1CService = $order1CService;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
    }

    /**
     * @param OrderEvent $orderEvent
     */
    public function sendNewOrder(OrderEvent $orderEvent): void
    {
        /** @var Order $order */
        $order = $this->em->find(Order::class, $orderEvent->getOrder()->getId());
        if ($order) {
            $this->order1CService->sendTo1c($order);

            $this->dispatcher->dispatch(
                OrderEvent::EVENT_ORDER_SENT,
                new OrderEvent($order, $orderEvent->getIsSendNotification())
            );
        }
    }
}
