<?php

namespace App\Service\Subscription;

use App\Component\Bonus\BonusException;
use App\Component\Delivery\DeliveryException;
use App\Component\Payment\PaymentException;
use App\Component\Product\ProductSearchComponent;
use App\Component\Product\ProductSearchException;
use App\Component\Product\Response\Product;
use App\Component\RequestResponseException;
use App\Component\UserService\UserServiceException;
use App\Component\UserService\UserServiceResponseException;
use App\DTO\ListResult;
use App\DTO\BasketChecked\BasketCheckedItem;
use App\DTO\Pagination;
use App\DTO\Subscription\AdminSubscriptionOrderCreate;
use App\DTO\Subscription\AdminSubscriptionSearch;
use App\Entity\Order;
use App\DTO\Subscription\Subscription as SubscriptionDTO;
use App\Entity\Subscription;
use App\Entity\SubscriptionItem;
use App\Exception\ObjectNotFoundException;
use App\Exception\SubscriptionException;
use App\Repository\OrderRepository;
use App\Repository\SubscriptionRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class AdminSubscriptionService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var SubscriptionOrderService */
    private $subscriptionOrderService;

    /** @var ProductSearchComponent */
    protected $productSearchComponent;

    /**
     * SubscriptionService constructor.
     * @param EntityManagerInterface $em
     * @param SubscriptionOrderService $subscriptionOrderService
     * @param ProductSearchComponent $productSearchComponent
     */
    public function __construct(
        EntityManagerInterface $em,
        SubscriptionOrderService $subscriptionOrderService,
        ProductSearchComponent $productSearchComponent
    ) {
        $this->em = $em;
        $this->subscriptionOrderService = $subscriptionOrderService;
        $this->productSearchComponent = $productSearchComponent;
    }

    public function getList(AdminSubscriptionSearch $search): ListResult
    {
        /** @var SubscriptionRepository $repository */
        $repository = $this->em->getRepository(Subscription::class);

        $list = $repository->findListAdmin($search, !empty($search->productId));

        return new ListResult($list->items, $list->count);
    }

    /**
     * @param int $id
     * @return ListResult
     * @throws ObjectNotFoundException
     */
    public function getOrderList(int $id): ListResult
    {
        $subscription = $this->find($id);

        /** @var OrderRepository $repository */
        $repository = $this->em->getRepository(Order::class);
        $list = $repository->findBy(['subscription' => $subscription]);

        return new ListResult($list, count($list));
    }

    /**
     * @param int $id
     * @return Subscription
     * @throws ObjectNotFoundException
     */
    public function find(int $id): Subscription
    {
        /** @var Subscription $subscription */
        if ($subscription = $this->em->find(Subscription::class, $id)) {
            return $subscription;
        }

        throw new ObjectNotFoundException('Subscription not found');
    }

    /**
     * @param int $id
     * @param SubscriptionDTO $dto
     * @return Subscription
     * @throws ObjectNotFoundException
     * @throws ProductSearchException
     * @throws RequestResponseException
     */
    public function update(int $id, SubscriptionDTO $dto): Subscription
    {
        $subscription = $this->find($id);

        $subscription->updateFromDto($dto);


        $nomenclatureList = [];
        foreach ($subscription->getSubscriptionItems() as $subscriptionItem) {
            $nomenclatureList[] = $subscriptionItem->getInternalId();
        }
        $this->productSearchComponent->searchProducts($subscription->getProject(), $nomenclatureList);


        /** @var SubscriptionItem $subscriptionItem */
        foreach ($subscription->getSubscriptionItems() as &$subscriptionItem) {
            $product = $this->productSearchComponent->getFromContainer(
                $subscription->getProject(),
                $subscriptionItem->getInternalId()
            );

            $subscriptionItem->fillFromProductSearch($product);
        }
        unset($subscriptionItem);

        $this->em->persist($subscription);
        $this->em->flush();

        return $subscription;
    }

    /**
     * @param int $id
     * @throws ObjectNotFoundException
     */
    public function delete(int $id): void
    {
        $subscription = $this->find($id);
        $this->em->remove($subscription);
        $this->em->flush();
    }

    /**
     * @param int $id
     * @param AdminSubscriptionOrderCreate $dto
     * @return Order
     * @throws BonusException
     * @throws DeliveryException
     * @throws ObjectNotFoundException
     * @throws ProductSearchException
     * @throws RequestResponseException
     * @throws UserServiceException
     * @throws UserServiceResponseException
     * @throws SubscriptionException
     */
    public function createOrder(int $id, AdminSubscriptionOrderCreate $dto): Order
    {
        $date = $dto->forDate ?? (new DateTime())->setTime(0, 0, 0, 0);

        if ($order = $this->subscriptionOrderService->createOrder($this->find($id), $date)) {
            return $order;
        }

        throw new SubscriptionException('Subscription not enable or items not in date', 400);
    }

    /**
     * @param AdminSubscriptionSearch $dto
     * @return BasketCheckedItem[]
     * @throws Exception
     */
    public function getProductPlanningList(AdminSubscriptionSearch $dto): array
    {
        $startDate = $dto->dateFrom;
        $endDate = $dto->dateTo;

        $dto->status = Subscription::STATUS_APPROVED;
        $dto->isActive = true;
        $dto->dateFrom = null;
        $dto->pagination = new Pagination();
        $dto->pagination->page = 1;
        $dto->pagination->limit = PHP_INT_MAX;

        $list = $this->getList($dto);

        $result = [];

        /** @var Subscription $subscription */
        foreach ($list->getItems() as $subscription) {
            $date = $subscription->getNextEnableDate(clone($startDate));
            while ($date !== null && $date <= $endDate) {
                foreach ($subscription->getEnableItemsForDate($date) as $subscriptionItem) {
                    $internalId = $subscriptionItem->getInternalId();

                    /** @var BasketCheckedItem|null $basketItem */
                    $basketItem = $result[$internalId] ?? null;
                    $quantity = $subscriptionItem->getQuantity() + ($basketItem ? $basketItem->getExpectedQuantity() : 0);

                    $basketItem = new BasketCheckedItem($internalId, $quantity);

                    $product = new Product();
                    $product->productId = $subscriptionItem->getProductId();

                    $basketItem->setProduct($product);

                    $result[$subscriptionItem->getInternalId()] = $basketItem;
                }
                $date = $subscription->getNextEnableDate($date, true);
            }
        }

        return array_values($result);
    }
}
