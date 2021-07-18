<?php

namespace App\EventListener;

use App\Component\Bonus\BonusException;
use App\Component\Delivery\DeliveryException;
use App\Component\Payment\PaymentException;
use App\Component\Product\ProductSearchException;
use App\Component\RequestResponseException;
use App\Component\UserService\UserServiceException;
use App\Component\UserService\UserServiceResponseException;
use App\Entity\Subscription;
use App\Event\SubscriptionEvent;
use App\Exception\ObjectNotFoundException;
use App\Service\Subscription\SubscriptionOrderService;
use Doctrine\ORM\EntityManagerInterface;

class OrderFromSubscriptionListener
{
    /** @var SubscriptionOrderService */
    private $service;

    /** @var EntityManagerInterface */
    private $em;

    public function __construct(SubscriptionOrderService $service, EntityManagerInterface $em)
    {
        $this->service = $service;
        $this->em = $em;
    }

    /**
     * @param SubscriptionEvent $event
     * @throws BonusException
     * @throws DeliveryException
     * @throws PaymentException
     * @throws ProductSearchException
     * @throws RequestResponseException
     * @throws UserServiceException
     * @throws UserServiceResponseException
     * @throws ObjectNotFoundException
     */
    public function createOrderForDate(SubscriptionEvent $event): void
    {
        /** @var Subscription $subscription */
        $subscription = $this->em->find(Subscription::class, $event->getSubscription()->getId());

        $this->service->createOrder($subscription, $event->getForDate());
    }
}
