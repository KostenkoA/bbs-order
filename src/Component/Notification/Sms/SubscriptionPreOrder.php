<?php

namespace App\Component\Notification\Sms;

use App\Component\Notification\NotificationAbstract;
use App\Component\Notification\SmsNotificationInterface;

class SubscriptionPreOrder extends NotificationAbstract implements SmsNotificationInterface
{
    public function getTemplate(): string
    {
        return 'subscription_pre_order';
    }
}
