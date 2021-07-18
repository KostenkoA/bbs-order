<?php

namespace App\Component\Delivery;

use App\Component\Delivery\Model\DeliveryOrderModel;

interface DeliveryCarrierInterface
{
    /**
     * @param DeliveryTypeInterface $deliveryType
     */
    public function setDeliveryType(DeliveryTypeInterface $deliveryType): void;

    /**
     * @param DeliveryTypeInterface $deliveryType
     * @return bool
     */
    public function checkDeliveryTypeEnable(DeliveryTypeInterface $deliveryType): bool;

    /**
     * @param DeliveryOrderModel $deliveryOrder
     * @return mixed
     */
    public function setDeliveryOrder(DeliveryOrderModel $deliveryOrder): void;

    /**
     * @return float
     */
    public function getDeliveryPrice(): float;
}
