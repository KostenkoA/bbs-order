<?php

namespace App\Component\Notification\Email;

use App\Component\Notification\EmailNotificationInterface;
use App\Component\Notification\NotificationAbstract;

class NewOrder extends NotificationAbstract implements EmailNotificationInterface
{
    public function getTemplate(): string
    {
        return 'new_order';
    }
}
