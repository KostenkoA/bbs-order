<?php

namespace App\Component\Notification\Sms;

use App\Component\Notification\NotificationAbstract;
use App\Component\Notification\SmsNotificationInterface;

class NewOrder extends NotificationAbstract implements SmsNotificationInterface
{
    public function getTemplate(): string
    {
        return 'new_order';
    }
}
