<?php

namespace App\Component\Delivery\Carrier;

use App\Component\Delivery\DeliveryCarrierInterface;
use App\Component\Delivery\DeliveryException;
use App\Component\Delivery\DeliveryTypeInterface;
use App\Component\Delivery\Model\DeliveryOrderModel;

abstract class AbstractCarrier implements DeliveryCarrierInterface
{
    /**
     * @var DeliveryTypeInterface;
     */
    protected $deliveryType;

    /**
     * @var DeliveryOrderModel
     */
    protected $deliveryOrder;

    /**
     * @param DeliveryTypeInterface $deliveryType
     * @throws DeliveryException
     */
    public function setDeliveryType(DeliveryTypeInterface $deliveryType): void
    {
        if (!$this->checkDeliveryTypeEnable($deliveryType)) {
            throw new DeliveryException(
                sprintf('Delivery type %s for %s not enable', get_class($deliveryType), get_class($this)),
                500
            );
        }
        $this->deliveryType = $deliveryType;
    }

    /**
     * @param DeliveryOrderModel $deliveryOrder
     */
    public function setDeliveryOrder(DeliveryOrderModel $deliveryOrder): void
    {
        $this->deliveryOrder = $deliveryOrder;
    }

    /**
     * @param DeliveryTypeInterface $deliveryType
     * @return bool
     */
    public function checkDeliveryTypeEnable(DeliveryTypeInterface $deliveryType): bool
    {
        return false;
    }

    /**
     * @return float
     */
    public function getDeliveryPrice(): float
    {
        return 0;
    }
}
