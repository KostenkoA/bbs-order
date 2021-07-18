<?php

namespace App\Producer;

use App\DTO\OneC\Order1C;
use App\Interfaces\OrderSend1CInterface;
use Interop\Queue\Exception;
use Interop\Queue\InvalidDestinationException;
use Interop\Queue\InvalidMessageException;

class Order1CProducer extends ProducerAbstract implements OrderSend1CInterface
{
    protected const QUEUE_NAME = '1c-service-order-external';

    private const NEW_ORDER_PROCESSOR = '1c-service.order.update.processor';

    /**
     * @param Order1C $order
     *
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function sendNewOrder(Order1C $order): void
    {
        $this->send(
            self::NEW_ORDER_PROCESSOR,
            $this->serializer->serialize($order, 'json')
        );
    }
}
