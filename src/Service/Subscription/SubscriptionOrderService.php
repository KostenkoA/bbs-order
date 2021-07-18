<?php

namespace App\Service\Subscription;

use App\Builder\OrderBuilder;
use App\Component\Bonus\BonusException;
use App\Component\Delivery\DeliveryException;
use App\Component\Product\ProductSearchException;
use App\Component\RequestResponseException;
use App\Component\UserService\UserServiceException;
use App\Component\UserService\UserServiceResponseException;
use App\DTO\Basket\Basket;
use App\DTO\BasketChecked\BasketChecked;
use App\DTO\Basket\BasketItem;
use App\Entity\Order;
use App\Entity\Subscription;
use App\Event\OrderPaymentByCardEvent;
use App\Exception\ObjectNotFoundException;
use App\Security\User;
use App\Service\BasketService;
use App\Service\Order\CreateOrderService;
use App\Service\PaymentService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SubscriptionOrderService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var CreateOrderService */
    private $orderService;

    /** @var BasketService */
    private $basketService;

    /** @var OrderBuilder */
    private $orderBuilder;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /**
     * SubscriptionOrderService constructor.
     * @param EntityManagerInterface $em
     * @param EventDispatcherInterface $eventDispatcher
     * @param OrderBuilder $orderBuilder
     * @param BasketService $basketService
     * @param CreateOrderService $orderService
     */
    public function __construct(
        EntityManagerInterface $em,
        EventDispatcherInterface $eventDispatcher,
        OrderBuilder $orderBuilder,
        BasketService $basketService,
        CreateOrderService $orderService
    ) {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
        $this->orderBuilder = $orderBuilder;
        $this->basketService = $basketService;
        $this->orderService = $orderService;
    }

    /**
     * @param Subscription $subscription
     * @param DateTime $date
     * @return Order
     * @throws BonusException
     * @throws DeliveryException
     * @throws ProductSearchException
     * @throws RequestResponseException
     * @throws UserServiceException
     * @throws UserServiceResponseException
     */
    public function createOrder(Subscription $subscription, DateTime $date): ?Order
    {
        if ($subscription->isEnableForOrder() && $subscription->isEnableForDate($date)) {
            $dto = $this->orderBuilder->buildNewOrderFromSubscription($subscription, $date);

            $order = $this->orderService->createByRegistered(
                $dto,
                new User($subscription->getUserRef()),
                $subscription->getProject()
            );

            $order->setSubscription($subscription);

            $this->em->persist($order);
            $this->em->flush();


            if ($subscription->getCard()) {
                $this->eventDispatcher->dispatch(
                    OrderPaymentByCardEvent::EVENT_NAME_PAYMENT,
                    new OrderPaymentByCardEvent($order, $subscription->getCard())
                );
            }

            return $order;
        }

        return null;
    }

    /**
     * @param Subscription $subscription
     * @param DateTime $date
     * @return BasketChecked|null
     */
    public function getSubscriptionBasket(Subscription $subscription, DateTime $date): ?BasketChecked
    {
        if ($subscription->isEnableForOrder() && $subscription->isEnableForDate($date)) {
            $dto = new Basket();
            $dto->project = $subscription->getProject();
            $dto->phone = $subscription->getPhone();

            foreach ($subscription->getSubscriptionItems() as $subscriptionItem) {
                if ($subscriptionItem->isEnableForDate($date)) {
                    $basketItem = new BasketItem();
                    $basketItem->internalId = $subscriptionItem->getInternalId();
                    $basketItem->quantity = $subscriptionItem->getQuantity();

                    $dto->basketItems[] = $basketItem;
                }
            }

            return $this->basketService->checkByBasket($dto);
        }

        return null;
    }
}
