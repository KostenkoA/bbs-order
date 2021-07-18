<?php

namespace App\EventListener;

use App\Component\Notification\Notification;
use App\Event\OrderEvent;
use Interop\Queue\Exception;
use Interop\Queue\InvalidDestinationException;
use Interop\Queue\InvalidMessageException;

class EmailListener
{
    /**
     * @var Notification
     */
    private $notification;

    /**
     * EmailListener constructor.
     * @param Notification $notification
     */
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    /**
     * @param OrderEvent $event
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function sendNew(OrderEvent $event): void
    {
        $order = $event->getOrder();

        if ($order && $order->getEmail() && $event->getIsSendNotification()) {
            $this->notification->sendNewOrderEmail($event->getOrder());
        }
    }
}
