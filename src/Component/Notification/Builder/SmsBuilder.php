<?php

namespace App\Component\Notification\Builder;

use App\Component\Notification\Sms\NewOrder;
use App\Component\Notification\Sms\SubscriptionPreOrder;
use App\Component\Notification\SmsNotificationInterface;
use App\DTO\BasketChecked\BasketChecked;
use App\Entity\Order;
use App\Entity\Subscription;
use DateTime;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SmsBuilder
{
    /**
     * @var string;
     */
    private $locale;

    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    /**
     * EmailBuilder constructor.
     * @param $locale
     * @param NormalizerInterface $normalizer
     */
    public function __construct($locale, NormalizerInterface $normalizer)
    {
        $this->locale = $locale;
        $this->normalizer = $normalizer;
    }

    /**
     * @param Order $order
     * @return SmsNotificationInterface
     */
    public function buildNewOrderSms(Order $order): SmsNotificationInterface
    {
        return new NewOrder(
            $order->getPhone(),
            $order->getProjectName(),
            $this->locale,
            [
                'order' => $this->normalizer->normalize($order, 'array', ['groups' => ['sms']]),
            ]
        );
    }

    public function buildSubscriptionPreOrder(
        Subscription $subscription,
        DateTime $forDate,
        BasketChecked $basketChecked
    ): SubscriptionPreOrder {
        return new SubscriptionPreOrder(
            $subscription->getPhone(),
            $subscription->getProject(),
            $this->locale,
            [
                'cost' => $basketChecked->getCost(),
                'date' => $forDate,
            ]
        );
    }
}
