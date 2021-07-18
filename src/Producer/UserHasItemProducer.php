<?php

namespace App\Producer;

use App\DTO\UserOrderItemCheck;
use Interop\Queue\Exception;
use Interop\Queue\InvalidDestinationException;
use Interop\Queue\InvalidMessageException;

class UserHasItemProducer extends ProducerAbstract
{
    /**
     * @param UserOrderItemCheck $reviewCheckOrder
     * @param string $queue
     * @param string $processor
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function sendCheck(UserOrderItemCheck $reviewCheckOrder, string $queue, string $processor): void
    {
        if ($processor && $queue) {
            $this->send(
                $processor,
                $this->serializer->serialize($reviewCheckOrder, 'json', ['groups' => ['send']]),
                $queue
            );
        }
    }
}
