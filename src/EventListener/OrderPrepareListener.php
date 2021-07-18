<?php

namespace App\EventListener;

use App\Component\Delivery\DeliverySearchException;
use App\Component\Delivery\DeliverySearchResponseException;
use App\Entity\Order;
use App\Event\OrderEvent;
use App\Service\Order\OrderPrepareService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OrderPrepareListener
{
    /**
     * @var OrderPrepareService
     */
    protected $orderPrepareService;
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * OrderPrepareListener constructor.
     * @param OrderPrepareService $orderPrepareService
     * @param EventDispatcherInterface $dispatcher
     * @param EntityManagerInterface $em
     */
    public function __construct(
        OrderPrepareService $orderPrepareService,
        EventDispatcherInterface $dispatcher,
        EntityManagerInterface $em
    ) {
        $this->orderPrepareService = $orderPrepareService;
        $this->dispatcher = $dispatcher;
        $this->em = $em;
    }

    /**
     * @param OrderEvent $orderEvent
     */
    public function prepareNewOrder(OrderEvent $orderEvent): void
    {
        /** @var Order $order */
        $order = $this->em->find(Order::class, $orderEvent->getOrder()->getId());
        if ($order) {
            try {
                $this->orderPrepareService->prepareOrder($order);
            } catch (DeliverySearchException $e) {
            } catch (DeliverySearchResponseException $e) {
            }
            $this->dispatcher->dispatch(OrderEvent::EVENT_ORDER_PREPARED, new OrderEvent($order, $orderEvent->getIsSendNotification()));
        }
    }
}
