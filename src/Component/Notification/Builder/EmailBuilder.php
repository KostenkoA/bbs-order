<?php

namespace App\Component\Notification\Builder;

use App\Component\Notification\Email\NewOrder;
use App\Component\Notification\EmailNotificationInterface;
use App\Entity\Order;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class EmailBuilder
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
     * @return EmailNotificationInterface
     */
    public function buildNewOrderEmail(Order $order): EmailNotificationInterface
    {
        return new NewOrder(
            $order->getEmail(),
            $order->getProjectName(),
            $this->locale,
            ['order' => $this->normalizer->normalize($order, 'array', ['groups' => ['email']])]
        );
    }
}
