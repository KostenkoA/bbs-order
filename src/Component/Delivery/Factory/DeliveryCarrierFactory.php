<?php

namespace App\Component\Delivery\Factory;

use App\Component\Delivery\DeliveryCarrierInterface;
use App\Component\Delivery\DeliveryException;

class DeliveryCarrierFactory
{
    /**
     * @var DeliveryCarrierInterface[]
     */
    private $enableCarriers;

    /**
     * DeliveryCarrierFactory constructor.
     * @param DeliveryCarrierInterface[]|null $enableCarriers
     */
    public function __construct(?array $enableCarriers = null)
    {
        $this->enableCarriers = $enableCarriers ?? [];
    }

    /**
     * @param $carrier
     * @return bool
     */
    public function checkTypeExist($carrier): bool
    {
        return !empty($this->enableCarriers[$carrier]);
    }

    /**
     * @param int|null $carrier
     * @return DeliveryCarrierInterface
     * @throws DeliveryException
     */
    public function create(?int $carrier): DeliveryCarrierInterface
    {
        if (!$this->checkTypeExist($carrier)) {
            throw new DeliveryException(sprintf('Carrier %s not found', $carrier), 500);
        }

        /** @var DeliveryCarrierInterface|null|mixed $carrierModel */
        $carrierModel = $this->enableCarriers[$carrier] ?? null;

        if (!$carrierModel) {
            throw new DeliveryException(sprintf('Carrier %s not found', $carrier), 500);
        }

        if (!($carrierModel instanceof DeliveryCarrierInterface)) {
            throw new DeliveryException(sprintf('Carrier %s not CarrierInterface', $carrier), 500);
        }

        return clone $carrierModel;
    }
}
