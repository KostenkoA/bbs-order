<?php

namespace App\Service\Order;

use App\DTO\OneC\OrderStatus1C;
use App\DTO\OneC\OrderStatus1CProduct;
use App\DTO\OneC\OrderStatusError1C;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\StatusInterface;
use App\Event\OrderErrorEvent;
use App\Event\OrderEvent;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Exception;

class OrderStatusService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * OrderStatusService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher)
    {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param OrderStatus1C $orderStatus
     * @return Order|null
     * @throws OrderFrom1cException
     */
    public function statusFrom1C(OrderStatus1C $orderStatus): ?Order
    {
        /** @var Order $order */
        $order = null;

        if ($orderStatus && $orderStatus->success) {
            try {
                $order = $this->setOrderStatus($orderStatus);
            } catch (Exception $exception) {
                throw new OrderFrom1cException($exception->getMessage());
            }
        } else {
            //TODO: тут должна быть в будущем обработка ошибок
        }


        return $order;
    }

    /**
     * @param OrderStatusError1C $orderStatusError
     * @return Order|null
     * @throws OrderNotFoundException
     */
    public function statusErrorFrom1C(OrderStatusError1C $orderStatusError): Order
    {
        /** @var OrderRepository $repository */
        $repository = $this->entityManager->getRepository(Order::class);

        if ($orderEntity = $repository->findOneByHash($orderStatusError->externalRef, null)) {
            $orderEntity->updateStatus(StatusInterface::STATUS_ERROR_FROM_1C);

            $this->entityManager->persist($orderEntity);
            $this->entityManager->flush();

            $this->dispatchStatusError($orderEntity, $orderStatusError);
        } else {
            throw new OrderNotFoundException(sprintf('Order with hash=%s', $orderStatusError->externalRef));
        }

        return $orderEntity;
    }

    /**
     * @param OrderStatus1C $orderStatus
     * @return Order|null
     * @throws OrderNotFoundException
     */
    protected function setOrderStatus(OrderStatus1C $orderStatus): ?Order
    {
        /** @var OrderRepository $repository */
        $repository = $this->entityManager->getRepository(Order::class);

        $orderEntity = null;
        if ($orderStatus->ref) {
            $orderEntity = $repository->findOneByRef($orderStatus->ref ?? '');
        }

        if (!$orderEntity) {
            $orderEntity = $repository->findOneByHash($orderStatus->getExternalRef(), null);
        }

        if ($orderEntity) {
            if (!$orderEntity->getRef()) {
                $orderEntity->updateRef($orderStatus->ref);
            }
            $orderEntity->updateStatus($orderStatus->status);
            $this->setOrderItemReserveState($orderStatus, $orderEntity);

            $this->entityManager->persist($orderEntity);
            $this->entityManager->flush();

            $this->dispatchStatusChanged($orderEntity);
        } else {
            throw new OrderNotFoundException(
                sprintf(
                    'Order with ref=%s or external ref=%s not found',
                    $orderStatus->ref,
                    $orderStatus->getExternalRef()
                )
            );
        }

        return $orderEntity;
    }

    private function setOrderItemReserveState(OrderStatus1C $orderStatus, Order $order): void
    {
        $statusProducts = $orderStatus->products;
        if ($statusProducts) {
            /** @var OrderItem $orderItem */
            foreach ($order->getOrderItems()->toArray() as $orderItem) {
                /** @var OrderStatus1CProduct $statusProduct */
                foreach ($statusProducts as $statusProduct) {
                    if ($statusProduct->ref === $orderItem->getInternalId()) {
                        $orderItem->updateFrom1c($statusProduct->reserveState, $statusProduct->deliveryDate);
                        break;
                    }
                }
            }
        }
    }

    /**
     * @param Order $order
     */
    private function dispatchStatusChanged(Order $order): void
    {
        $this->eventDispatcher->dispatch(OrderEvent::EVENT_STATUS_CHANGED, new OrderEvent($order));
    }

    /**
     * @param Order $order
     * @param OrderStatusError1C $orderStatusError
     */
    private function dispatchStatusError(Order $order, OrderStatusError1C $orderStatusError): void
    {
        $this->eventDispatcher->dispatch(
            OrderErrorEvent::EVENT_STATUS_ERROR,
            new OrderErrorEvent($order, $orderStatusError)
        );
    }
}
