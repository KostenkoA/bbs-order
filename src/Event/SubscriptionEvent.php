<?php

namespace App\Event;

use App\Entity\Subscription;
use DateTime;
use Symfony\Component\EventDispatcher\Event;

class SubscriptionEvent extends Event
{
    public const EVENT_ORDER_CREATE = 'app.subscription.create-order';

    public const EVENT_PREORDER_NOTIFICATION = 'app.subscription.pre-order-notification';

    /** @var Subscription */
    private $subscription;

    /** @var DateTime */
    private $forDate;

    public function __construct(Subscription $subscription, DateTime $forDate)
    {
        $this->subscription = $subscription;
        $this->forDate = $forDate;
    }

    /**
     * @return Subscription
     */
    public function getSubscription(): Subscription
    {
        return $this->subscription;
    }

    /**
     * @return DateTime
     */
    public function getForDate(): DateTime
    {
        return $this->forDate;
    }
}
