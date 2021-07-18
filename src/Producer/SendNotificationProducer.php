<?php

namespace App\Producer;

use App\Component\Notification\EmailNotificationInterface;
use App\Component\Notification\SmsNotificationInterface;
use Interop\Queue\Exception;
use Interop\Queue\InvalidDestinationException;
use Interop\Queue\InvalidMessageException;

class SendNotificationProducer extends ProducerAbstract
{
    protected const QUEUE_NAME = 'notification-service-external';

    /**
     * @param EmailNotificationInterface $notification
     *
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function sendEmail(EmailNotificationInterface $notification): void
    {
        $this->send(
            'bbs-notification.email.processor',
            $this->serializer->serialize($notification, 'json')
        );
    }

    /**
     * @param SmsNotificationInterface $notification
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function sendSms(SmsNotificationInterface $notification): void
    {
        $this->send(
            'bbs-notification.sms.processor',
            $this->serializer->serialize($notification, 'json')
        );
    }
}
