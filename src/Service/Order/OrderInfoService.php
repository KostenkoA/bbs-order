<?php

namespace App\Service\Order;

use App\DTO\AdminOrderSearch;
use App\DTO\OrderSearch;
use App\DTO\OrderSearchResult;
use App\Entity\Order;
use App\Exception\ObjectNotFoundException;
use App\Exception\UserRefOrderException;
use App\Interfaces\FindOrderInterface;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class OrderInfoService implements FindOrderInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var NormalizerInterface
     */
    protected $normalizer;

    public function __construct(
        EntityManagerInterface $entityManager,
        NormalizerInterface $normalizer
    ) {
        $this->entityManager = $entityManager;
        $this->normalizer = $normalizer;
    }

    /**
     * @param AdminOrderSearch $orderSearch
     * @return OrderSearchResult
     */
    public function getAdminList(AdminOrderSearch $orderSearch): OrderSearchResult
    {
        /** @var OrderRepository $repository */
        $repository = $this->entityManager->getRepository(Order::class);
        $paginated = $repository->findAdminList($orderSearch);

        return new OrderSearchResult($paginated->items, $paginated->count, $paginated->getFirstResult());
    }

    /**
     * @param OrderSearch $orderSearch
     * @return OrderSearchResult
     */
    public function getList(OrderSearch $orderSearch): OrderSearchResult
    {
        /** @var OrderRepository $repository */
        $repository = $this->entityManager->getRepository(Order::class);
        $paginated = $repository->findList($orderSearch);

        return new OrderSearchResult($paginated->items, $paginated->count, $paginated->getFirstResult());
    }

    /**
     * @param string $hash
     * @param string $projectName
     * @return Order
     * @throws ObjectNotFoundException
     */
    public function getByHash(string $hash, string $projectName): Order
    {
        /** @var OrderRepository $repository */
        $repository = $this->entityManager->getRepository(Order::class);

        if ($order = $repository->findOneByHash($hash, $projectName)) {
            return $order;
        }

        throw new ObjectNotFoundException(sprintf('Order by hash %s not found', $hash));
    }

    /**
     * @param string $hash
     * @param string $projectName
     * @param string|null $userRef
     * @return Order
     * @throws ObjectNotFoundException
     */
    public function findForUser(string $hash, string $projectName, ?string $userRef): Order
    {
        /** @var OrderRepository $repository */
        $repository = $this->entityManager->getRepository(Order::class);

        if ($order = $repository->findOneByHash($hash, $projectName)) {
            if (
                !($userRef === null && $order->getFromAnon()) &&
                $order->getUserRef() !== $userRef
            ) {
                throw new UserRefOrderException(sprintf('Wrong userRef for order %s', $order->getHash()));
            }

            return $order;
        }

        throw new ObjectNotFoundException(sprintf('Order by hash %s for current user not found', $hash));
    }

    /**
     * @param int $id
     * @return Order
     * @throws ObjectNotFoundException
     */
    public function findById(int $id): Order
    {
        /** @var OrderRepository $repository */
        $repository = $this->entityManager->getRepository(Order::class);

        /** @var Order|null $order */
        if ($order = $repository->find($id)) {
            return $order;
        }

        throw new ObjectNotFoundException(sprintf('Order by id %s not found', $id));
    }
}
