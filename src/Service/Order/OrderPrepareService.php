<?php

namespace App\Service\Order;

use App\Component\Delivery\DeliveryComponent;
use App\Component\Delivery\DeliverySearchException;
use App\Component\Delivery\DeliverySearchResponseException;
use App\Entity\DeliveryTypeInterface;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class OrderPrepareService
{
    /** @var EntityManagerInterface */
    protected $em;

    /** @var DeliveryComponent */
    private $deliveryComponent;

    /**
     * OrderPrepareService constructor.
     * @param EntityManagerInterface $entityManager
     * @param DeliveryComponent $deliveryComponent
     */
    public function __construct(EntityManagerInterface $entityManager, DeliveryComponent $deliveryComponent)
    {
        $this->em = $entityManager;
        $this->deliveryComponent = $deliveryComponent;
    }

    /**
     * @param Order $order
     * @return void
     * @throws DeliverySearchException
     * @throws DeliverySearchResponseException
     */
    public function prepareOrder(Order $order): void
    {
        if ($order->getCityRef()) {
            switch ($order->getDeliveryType()) {
                case DeliveryTypeInterface::DELIVERY_BRANCH:
                    $this->prepareBranch($order);
                    break;
                case DeliveryTypeInterface::DELIVERY_ADDRESS:
                    $this->prepareAddress($order);
                    break;
            }
        }
    }

    /**
     * @param Order $order
     * @throws DeliverySearchException
     * @throws DeliverySearchResponseException
     */
    private function prepareBranch(Order $order): void
    {
        $settlementResponse = $this->deliveryComponent->getWarehouse(
            $order->getCityRef(),
            $order->getDeliveryCarrier(),
            $order->getDeliveryBranchRef()
        );

        if ($settlement = $settlementResponse->settlement) {
            $order->updateFromDelivery($settlement, null, $settlementResponse->warehouse);
            $this->saveOrder($order);
        }
    }

    /**
     * @param Order $order
     * @throws DeliverySearchException
     * @throws DeliverySearchResponseException
     */
    private function prepareAddress(Order $order): void
    {
        $settlementResponse = $this->deliveryComponent->getStreet(
            $order->getCityRef(),
            $order->getStreetRef()
        );

        if ($settlement = $settlementResponse->settlement) {
            $order->updateFromDelivery($settlement, $settlementResponse->street);
            $this->saveOrder($order);
        }
    }

    /**
     * @param Order $order
     */
    private function saveOrder(Order $order): void
    {
        $this->em->persist($order);
        $this->em->flush();
        $this->em->refresh($order);
    }
}
