<?php

namespace App\Event;

use App\Entity\Order;
use App\Interfaces\OrderEventInterface;
use App\Interfaces\SendMessagePropertyInterface;
use App\Traits\OrderPropertyTrait;
use Symfony\Component\EventDispatcher\Event;

class OrderEvent extends Event implements OrderEventInterface, SendMessagePropertyInterface
{
    use OrderPropertyTrait;

    /**
     * @var bool
     */
    protected $isSendNotification;

    public const EVENT_NEW_ORDER = 'app.order.new';

    public const EVENT_ORDER_PREPARED = 'app.order.prepared';

    public const EVENT_ORDER_SENT = 'app.order.1c-sent';

    public const EVENT_STATUS_CHANGED = 'app.order.status-changed';

    public const EVENT_USER_REGISTRATION = 'app.order.user-registration';

    /**
     * NewOrderEvent constructor.
     *
     * @param Order $order
     * @param bool $isSendNotification
     */
    public function __construct(Order $order, bool $isSendNotification = true)
    {
        $this->order = $order;
        $this->isSendNotification = $isSendNotification;
    }

    /**
     * @return bool
     */
    public function getIsSendNotification(): bool
    {
        return $this->isSendNotification;
    }
}
