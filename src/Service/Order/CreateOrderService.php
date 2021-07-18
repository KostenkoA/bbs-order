<?php

namespace App\Service\Order;

use App\Builder\UserBuilder;
use App\Component\Bonus\BonusComponent;
use App\Component\Bonus\BonusException;
use App\Component\Delivery\DeliveryComponent;
use App\Component\Delivery\DeliveryException;
use App\Component\Product\Response\Product;
use App\Component\RequestResponseException;
use App\Component\UserService\UserServiceException;
use App\Component\UserService\UserServiceResponseException;
use App\DTO\NewAdminOrder;
use App\DTO\NewOrder;
use App\Entity\DeliveryTypeInterface;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\PaymentTypeInterface;
use App\Event\OrderEvent;
use App\Event\UserEvent;
use App\Security\User;
use App\Component\Product\ProductSearchException;
use App\Service\BasketService;
use App\Service\Subscription\SubscriptionService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CreateOrderService
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var BasketService
     */
    private $basketService;

    /**
     * @var DeliveryComponent
     */
    protected $deliveryComponent;

    /**
     * @var SubscriptionService
     */
    private $subscriptionService;

    /**
     * @var BonusComponent
     */
    protected $bonusComponent;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var UserBuilder
     */
    protected $userBuilder;


    /**
     * @param EntityManagerInterface $entityManager
     * @param EventDispatcherInterface $eventDispatcher
     * @param BasketService $basketService
     * @param DeliveryComponent $deliveryComponent
     * @param SubscriptionService $subscriptionService
     * @param UserBuilder $userBuilder
     * @param BonusComponent $bonusComponent
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        BasketService $basketService,
        DeliveryComponent $deliveryComponent,
        SubscriptionService $subscriptionService,
        UserBuilder $userBuilder,
        BonusComponent $bonusComponent
    ) {
        $this->em = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->basketService = $basketService;
        $this->deliveryComponent = $deliveryComponent;
        $this->subscriptionService = $subscriptionService;
        $this->userBuilder = $userBuilder;
        $this->bonusComponent = $bonusComponent;
    }

    /**
     * @param NewOrder $orderDTO
     * @return Order
     * @throws ProductSearchException
     * @throws DeliveryException
     * @throws RequestResponseException
     */
    public function createByAnonymous(NewOrder $orderDTO): Order
    {
        $order = $this->fillNewOrder($orderDTO, true);
        $order = $this->fillByCheckBasket($orderDTO, $order);

        $order = $this->fillDeliveryPrice($order);


        return $this->saveOrder($order, $this->getIsNeedSendNotification($orderDTO));
    }

    /**
     * @param NewOrder $orderDTO
     * @param User $user
     * @return Order
     * @throws BonusException
     * @throws DeliveryException
     * @throws ProductSearchException
     * @throws BonusException
     * @throws UserServiceException
     * @throws UserServiceResponseException
     * @throws RequestResponseException
     * @throws Exception
     */
    public function createByRegistered(NewOrder $orderDTO, User $user): Order
    {
        $orderDTO->userRef = $user->getId();

        $order = $this->fillNewOrder($orderDTO);
        $order = $this->fillByCheckBasket($orderDTO, $order);

        if ($orderDTO->usedBonuses) {
            $order->calculatePrice();
            $usedBonuses = $this->bonusComponent->calculateBonusExist(
                $user,
                $orderDTO->usedBonuses,
                $order->getPrice()
            );
            if ($usedBonuses) {
                $order->updateUsedBonuses($usedBonuses->getDiscountAmount(), $usedBonuses->getBonusPoints());
            }
        }
        $order = $this->fillDeliveryPrice($order);

        $order = $this->saveOrder($order, $this->getIsNeedSendNotification($orderDTO));

        if (!empty($orderDTO->subscriptionItems)) {
            $this->subscriptionService->createByOrder($order, $orderDTO->subscriptionItems);
        }

        return $order;
    }

    /**
     * @param NewOrder $orderDTO
     * @return Order
     * @throws DeliveryException
     * @throws ProductSearchException
     * @throws RequestResponseException
     */
    public function createByOneClick(NewOrder $orderDTO): Order
    {
        if ($orderDTO->deliveryType === DeliveryTypeInterface::DELIVERY_ADDRESS) {
            $orderDTO->deliveryCarrier = 0;
        }
        $orderDTO->paymentType = PaymentTypeInterface::PAYMENT_CASH;
        $orderDTO->comment = 'Заказ в 1 клик';

        $order = $this->createByAnonymous($orderDTO);

        $this->eventDispatcher->dispatch(
            UserEvent::EVENT_USER_SEND_FOR_ATTACH,
            new UserEvent($this->userBuilder->buildUserByNewOrder($orderDTO))
        );

        return $order;
    }

    /**
     * @param NewOrder $orderDTO
     * @return Order
     * @throws DeliveryException
     * @throws ProductSearchException
     * @throws RequestResponseException
     */
    public function createByAnonRegister(NewOrder $orderDTO): Order
    {
        $order = $this->createByAnonymous($orderDTO);

        $this->eventDispatcher->dispatch(
            UserEvent::EVENT_USER_REGISTRATION,
            new UserEvent($this->userBuilder->buildUserByNewOrder($orderDTO))
        );

        return $order;
    }

    /**
     * @param NewOrder $orderDTO
     * @return Order
     * @throws DeliveryException
     * @throws ProductSearchException
     * @throws RequestResponseException
     */
    public function createByAnon(NewOrder $orderDTO): Order
    {
        $order = $this->createByAnonymous($orderDTO);

        $this->eventDispatcher->dispatch(
            UserEvent::EVENT_USER_SEND_FOR_ATTACH,
            new UserEvent($this->userBuilder->buildUserByNewOrder($orderDTO))
        );

        return $order;
    }

    private function fillNewOrder(NewOrder $orderDTO, bool $fromAnon = false): Order
    {
        $order = new Order();
        $order->fillFromNewOrder($orderDTO, $fromAnon);

        return $order;
    }

    /**
     * @param NewOrder $dto
     * @param Order $order
     * @return Order
     * @throws ProductSearchException
     * @throws RequestResponseException
     */
    private function fillByCheckBasket(NewOrder $dto, Order $order): Order
    {
        $basketModel = $this->basketService->checkByNewOrder($dto);

        /** @var OrderItem $orderItem */
        foreach ($order->getOrderItems() as $orderItem) {
            $basketItem = $basketModel->findBasketItem($orderItem->getInternalId());

            if ($basketItem && $basketItem->getSearchException()) {
                throw $basketItem->getSearchException();
            }

            if (!$basketItem || !$basketItem->getProduct()) {
                throw new ProductSearchException(
                    sprintf('Nomenclature %s not found', $basketItem->getInternalId()),
                    500
                );
            }
            /** @var Product $product */
            $product = $basketItem->getProduct();

            if (!(float)$product->sellingPrice && (float)$product->sellingPrice <= 0) {
                throw new ProductSearchException(sprintf('Wrong price for item %s', $product->intervalId), 500);
            }

            $orderItem->fillFromBasketCheckedItem($basketItem);
        }

        foreach ($basketModel->getGiftLists() ?? [] as $giftList) {
            $gifts = $giftList->getGivenGifts();
            foreach ($gifts as $gift) {
                $order->addGiftOrderItem($gift, $giftList);
            }
        }
        $order->clearEmptyOrderItems();
        $order->calculateDiscountAmount();

        return $order;
    }

    /**
     * @param Order $order
     * @return Order
     * @throws DeliveryException
     * @throws Exception
     */
    private function fillDeliveryPrice(Order $order): Order
    {
        $order->calculatePrice();
        $order->updateDeliveryPrice($this->deliveryComponent->calculateDeliveryPrice($order));

        return $order;
    }

    /**
     * @param Order $order
     * @param bool $isSendMessage
     * @return Order
     */
    private function saveOrder(Order $order, bool $isSendMessage = true): Order
    {
        $this->em->persist($order);
        $this->em->flush();
        $this->em->refresh($order);

        $this->eventDispatcher->dispatch(OrderEvent::EVENT_NEW_ORDER, new OrderEvent($order, $isSendMessage));

        return $order;
    }

    /**
     * @param NewOrder $orderDto
     * @return bool
     */
    private function getIsNeedSendNotification(NewOrder $orderDto): bool
    {
        if ($orderDto instanceof NewAdminOrder) {
            return $orderDto->sendMessage;
        }

        return true;
    }
}
