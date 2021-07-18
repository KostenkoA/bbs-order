<?php

namespace App\Service\Subscription;

use App\Builder\SubscriptionBuilder;
use App\Component\Product\ProductSearchComponent;
use App\Component\Product\ProductSearchException;
use App\Component\RequestResponseException;
use App\DTO\ListResult;
use App\DTO\Pagination;
use App\DTO\Subscription\SubscriptionCard;
use App\Entity\Card;
use App\Entity\Order;
use App\DTO\Subscription\Subscription as SubscriptionDTO;
use App\Entity\Subscription;
use App\Entity\SubscriptionItem;
use App\Event\SubscriptionEvent;
use App\Exception\ObjectNotFoundException;
use App\Exception\SubscriptionException;
use App\Repository\CardRepository;
use App\Repository\OrderRepository;
use App\Repository\SubscriptionRepository;
use App\Security\User;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionService
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var ProductSearchComponent */
    protected $productSearchComponent;

    /** @var SubscriptionBuilder */
    private $subscriptionBuilder;

    /**
     * SubscriptionService constructor.
     * @param EntityManagerInterface $em
     * @param SubscriptionBuilder $subscriptionBuilder
     * @param ProductSearchComponent $productSearchComponent
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        EntityManagerInterface $em,
        SubscriptionBuilder $subscriptionBuilder,
        ProductSearchComponent $productSearchComponent,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->em = $em;
        $this->eventDispatcher = $eventDispatcher;
        $this->productSearchComponent = $productSearchComponent;
        $this->subscriptionBuilder = $subscriptionBuilder;
    }

    public function getAll(string $project, User $user): ListResult
    {
        /** @var SubscriptionRepository $repository */
        $repository = $this->em->getRepository(Subscription::class);

        $list = $repository->findBy(['project' => $project, 'userRef' => $user->getId()]);

        return new ListResult($list, count($list));
    }

    /**
     * @param string $project
     * @param User $user
     * @param int $id
     * @return \App\DTO\ListResult
     * @throws ObjectNotFoundException
     */
    public function getOrderList(string $project, User $user, int $id): ListResult
    {
        $subscription = $this->find($project, $user, $id);

        /** @var OrderRepository $repository */
        $repository = $this->em->getRepository(Order::class);
        $list = $repository->findBy(['subscription' => $subscription]);

        return new ListResult($list, count($list));
    }

    /**
     * @param string $project
     * @param User $user
     * @param int $id
     * @return Subscription
     * @throws ObjectNotFoundException
     */
    public function find(string $project, User $user, int $id): Subscription
    {
        /** @var SubscriptionRepository $repository */
        $repository = $this->em->getRepository(Subscription::class);

        if ($subscription = $repository->findOneBy(['project' => $project, 'userRef' => $user->getId(), 'id' => $id])) {
            return $subscription;
        }

        throw new ObjectNotFoundException(sprintf('Subscription for project %s  for current user not found', $project));
    }

    /**
     * @param string $project
     * @param User $user
     * @return Subscription
     */
    public function findDefault(string $project, User $user): ?Subscription
    {
        /** @var SubscriptionRepository $repository */
        $repository = $this->em->getRepository(Subscription::class);

        return $repository->findOneDefault($project, $user->getId());
    }

    /**
     * @param string $project
     * @param User $user
     * @param int $id
     * @param SubscriptionDTO $dto
     * @return Subscription
     * @throws ObjectNotFoundException
     * @throws ProductSearchException
     * @throws RequestResponseException
     */
    public function update(string $project, User $user, int $id, SubscriptionDTO $dto): Subscription
    {
        $subscription = $this->find($project, $user, $id);

        $subscription->updateFromDto($dto);
        $this->psUpdateSubscriptionItems($subscription);

        $this->em->persist($subscription);
        $this->em->flush();

        return $subscription;
    }

    /**
     * @param string $project
     * @param User $user
     * @param int $id
     * @throws ObjectNotFoundException
     */
    public function delete(string $project, User $user, int $id): void
    {
        $subscription = $this->find($project, $user, $id);
        $this->em->remove($subscription);
        $this->em->flush();
    }

    /**
     * @param string $project
     * @param User $user
     * @param int $id
     * @param SubscriptionCard $dto
     * @return Subscription
     * @throws ObjectNotFoundException|SubscriptionException
     */
    public function updateCard(string $project, User $user, int $id, SubscriptionCard $dto): Subscription
    {
        $subscription = $this->find($project, $user, $id);

        /** @var CardRepository $repository */
        $repository = $this->em->getRepository(Card::class);
        $card = $repository->findByHash($project, $user->getId(), $dto->cardHash);

        if (!$card) {
            throw new ObjectNotFoundException(printf('Card for project %s for current user not found', $project));
        }

        if (!$card->getIsVerified()) {
            throw new SubscriptionException('Card must be verified', Response::HTTP_BAD_REQUEST);
        }

        $this->updateByCard($subscription, $card);

        return $subscription;
    }

    public function updateByCard(Subscription $subscription, Card $card): Subscription
    {
        $subscription->updateByCard($card);

        $this->em->persist($subscription);
        $this->em->flush();

        return $subscription;
    }

    /**
     * @param Order $order
     * @param array $subscriptionItems
     * @return Subscription
     * @throws ProductSearchException
     * @throws RequestResponseException
     */
    public function createByOrder(
        Order $order,
        array $subscriptionItems = []
    ): Subscription {
        $subscription = $this->findDefault($order->getProjectName(), new User($order->getUserRef())) ??
            new Subscription($order->getProjectName(), $order->getUserRef());

        $subscription = $this->subscriptionBuilder->fillFromOrder($subscription, $order, $subscriptionItems);

        $this->psUpdateSubscriptionItems($subscription);
        $order->setSubscription($subscription);

        $this->em->persist($subscription);
        $this->em->persist($order);
        $this->em->flush();

        return $subscription;
    }

    /**
     * @param Subscription $subscription
     * @throws ProductSearchException
     * @throws RequestResponseException
     */
    protected function psUpdateSubscriptionItems(Subscription $subscription): void
    {
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
    }

    public function eventForDate(DateTime $forDate, string $eventName): void
    {
        /** @var SubscriptionRepository $repository */
        $repository = $this->em->getRepository(Subscription::class);

        $pagination = new Pagination();

        do {
            $result = $repository->findActiveForDate($forDate, $pagination);
            /** @var Subscription $subscription */
            foreach ($result->items as $subscription) {
                $this->eventDispatcher->dispatch($eventName, new SubscriptionEvent($subscription, $forDate));
            }

            ++$pagination->page;
            ++$result->page;
            $nextFirst = $result->getFirstResult();
        } while ($result->count > $nextFirst);
    }
}
