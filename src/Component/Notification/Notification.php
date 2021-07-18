<?php

namespace App\Component\Notification;

use App\Component\Notification\Builder\EmailBuilder;
use App\Component\Notification\Builder\SmsBuilder;
use App\DTO\BasketChecked\BasketChecked;
use App\Entity\Subscription;
use App\Entity\Order;
use App\Producer\SendNotificationProducer;
use DateTime;
use Interop\Queue\Exception;
use Interop\Queue\InvalidDestinationException;
use Interop\Queue\InvalidMessageException;

class Notification
{
    /**
     * @var SendNotificationProducer
     */
    private $producer;

    /**
     * @var EmailBuilder
     */
    private $emailBuilder;

    /**
     * @var SmsBuilder
     */
    private $smsBuilder;

    /**
     * EmailNotification constructor.
     * @param SendNotificationProducer $producer
     * @param EmailBuilder $emailBuilder
     * @param SmsBuilder $smsBuilder
     */
    public function __construct(SendNotificationProducer $producer, EmailBuilder $emailBuilder, SmsBuilder $smsBuilder)
    {
        $this->producer = $producer;
        $this->emailBuilder = $emailBuilder;
        $this->smsBuilder = $smsBuilder;
    }

    /**
     * @param EmailNotificationInterface $email
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    private function sendEmail(EmailNotificationInterface $email): void
    {
        $this->producer->sendEmail($email);
    }

    /**
     * @param SmsNotificationInterface $sms
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    private function sendSms(SmsNotificationInterface $sms): void
    {
        $this->producer->sendSms($sms);
    }

    /**
     * @param Order $order
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function sendNewOrderEmail(Order $order): void
    {
        $this->sendEmail($this->emailBuilder->buildNewOrderEmail($order));
    }

    /**
     * @param Order $order
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function sendNewOrderSms(Order $order): void
    {
        $this->sendSms($this->smsBuilder->buildNewOrderSms($order));
    }

    /**
     * @param Subscription $subscription
     * @param DateTime $forDate
     * @param BasketChecked $basketChecked
     * @throws Exception
     * @throws InvalidDestinationException
     * @throws InvalidMessageException
     */
    public function sendSubscriptionPreOrderSms(
        Subscription $subscription,
        DateTime $forDate,
        BasketChecked $basketChecked
    ): void {
        $this->sendSms($this->smsBuilder->buildSubscriptionPreOrder($subscription, $forDate, $basketChecked));
    }
}
