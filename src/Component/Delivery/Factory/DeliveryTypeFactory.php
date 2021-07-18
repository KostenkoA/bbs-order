<?php

namespace App\Component\Delivery\Factory;

use App\Component\Delivery\DeliveryException;
use App\Component\Delivery\DeliveryTypeInterface;
use App\Component\Delivery\Type\Address;
use App\Component\Delivery\Type\Branch;
use App\Component\Delivery\Type\Shop;
use App\Entity\DeliveryTypeInterface as EntityDeliveryTypeInterface;

class DeliveryTypeFactory
{
    /**
     * @var DeliveryTypeInterface[]
     */
    private $enableTypes;

    /**
     * DeliveryTypeFactory constructor
     */
    public function __construct()
    {
        $this->enableTypes = [
            EntityDeliveryTypeInterface::DELIVERY_ADDRESS => new Address(),
            EntityDeliveryTypeInterface::DELIVERY_BRANCH => new Branch(),
            EntityDeliveryTypeInterface::DELIVERY_SHOP => new Shop(),
        ];
    }

    /**
     * @param $type
     * @return bool
     */
    public function checkTypeExist($type): bool
    {
        return !empty($this->enableTypes[$type]);
    }

    /**
     * @param int $type
     * @return DeliveryTypeInterface
     * @throws DeliveryException
     */
    public function create(int $type): DeliveryTypeInterface
    {
        if (!$this->checkTypeExist($type)) {
            throw new DeliveryException(sprintf('Delivery type %s not found', $type), 500);
        }

        $type = clone ($this->enableTypes[$type]);

        if (!($type instanceof DeliveryTypeInterface)) {
            throw new DeliveryException(sprintf('Delivery type %s not DeliveryTypeInterface', $type), 500);
        }

        return $type;
    }
}
