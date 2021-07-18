<?php

namespace App\Interfaces;

use App\Entity\Order;
use App\Exception\ObjectNotFoundException;

interface FindOrderInterface
{
    /**
     * @param int $id
     * @return Order
     * @throws ObjectNotFoundException
     */
    public function findById(int $id): Order;
}
