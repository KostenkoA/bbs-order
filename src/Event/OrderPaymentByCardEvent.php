<?php

namespace App\Event;

use App\Entity\Card;
use App\Entity\Order;
use App\Interfaces\OrderEventInterface;
use App\Traits\OrderPropertyTrait;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class OrderPaymentByCardEvent
 * @package App\Event
 */
class OrderPaymentByCardEvent extends Event implements OrderEventInterface
{
    public const EVENT_NAME_PAYMENT = 'app.order.payment-by-card';

    use OrderPropertyTrait;

    /**
     * @var Card
     */
    private $card;

    /**
     * NewOrderEvent constructor.
     *
     * @param Order $order
     * @param Card $card
     */
    public function __construct(Order $order, Card $card)
    {
        $this->order = $order;
        $this->card = $card;
    }

    public function getCard(): Card
    {
        return $this->card;
    }
}
