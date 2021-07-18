<?php

namespace App\EventListener;

use App\Component\Notification\Notification;
use App\Event\OrderEvent;
use App\Event\SubscriptionEvent;
use App\Service\Subscription\SubscriptionOrderService;
use Interop\Queue\Exception;
use Interop\Queue\InvalidDestinationException;
use Interop\Queue\InvalidMessageException;

class SmsListener
{
    /** @var Notification */
    private $notification;


    /** @var SubscriptionOrderService */
    private $orderSubscriptionService;

    /**
     * SmsListener constructor.
     * @param Notification $notification
     * @param SubscriptionOrderService $orderSubscriptionService
     */
    public function __construct(Notification $notification, SubscriptionOrderService $orderSubscriptionService)
    {
        $this->notification = $notification;
        $this->orderSubscriptionService = $orderSubscriptionService;
    }

    /**
     * @param OrderEvent $event
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function sendNewOrderNotification(OrderEvent $event): void
    {
        $order = $event->getOrder();

        if ($order && $order->getPhone() && $event->getIsSendNotification()) {
            $this->notification->sendNewOrderSms($event->getOrder());
        }
    }

    /**
     * @param SubscriptionEvent $event
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function sendSubscriptionPreOrder(SubscriptionEvent $event): void
    {
        $subscription = $event->getSubscription();
        $forDate = $event->getForDate();

        if ($subscription && $subscription->getPhone()) {
            $this->notification->sendSubscriptionPreOrderSms(
                $subscription,
                $forDate,
                $this->orderSubscriptionService->getSubscriptionBasket($subscription, $forDate)
            );
        }
    }
}
