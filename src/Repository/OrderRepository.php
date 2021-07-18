<?php

namespace App\Repository;

use App\DTO\AdminOrderSearch;
use App\DTO\OrderSearch;
use App\DTO\PaginatedResult;
use App\DTO\Sort;
use App\Entity\Order;
use App\Traits\Repository\PaginateTrait;
use App\Traits\Repository\QueryLikeTrait;
use Doctrine\ORM\EntityRepository;

/**
 * Class OrderRepository
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class OrderRepository extends EntityRepository
{
    use QueryLikeTrait;
    use PaginateTrait;

    /**
     * @param string $value
     * @return Order|null
     */
    public function findOneByNumber(string $value): ?Order
    {
        return $this->findOneBy(['number' => $value]);
    }

    /**
     * @param AdminOrderSearch $orderSearch
     * @return PaginatedResult
     */
    public function findAdminList(AdminOrderSearch $orderSearch): PaginatedResult
    {
        $qb = $this->createQueryBuilder('o');

        $this->addLikeIfNotNull($qb, 'o.ref', $orderSearch->ref);
        $this->addLikeIfNotNull($qb, 'o.firstName', $orderSearch->firstName);
        $this->addLikeIfNotNull($qb, 'o.lastName', $orderSearch->lastName);
        $this->addLikeIfNotNull($qb, 'o.number', $orderSearch->number);
        $this->addLikeIfNotNull($qb, 'o.phone', $orderSearch->phone);
        $this->addLikeIfNotNull($qb, 'o.email', $orderSearch->email);

        if ($orderSearch->userRef) {
            $qb->andWhere('o.userRef = :userRef')->setParameter('userRef', $orderSearch->userRef);
        }

        if (!empty($orderSearch->status)) {
            $qb->andWhere('o.status IN(:status)')->setParameter('status', (array)$orderSearch->status);
        }

        if ($orderSearch->projectName) {
            $qb->andWhere('o.projectName = :projectName')->setParameter('projectName', $orderSearch->projectName);
        }

        if ($orderSearch->createDate) {
            $createAtFrom = (clone $orderSearch->createDate)->setTime(0, 0);
            $createAtTo = (clone $orderSearch->createDate)->setTime(23, 59, 59);

            $qb->andWhere('(o.createdAt >= :createAtFrom and o.createdAt <= :createAtTo)');
            $qb->setParameter('createAtFrom', $createAtFrom);
            $qb->setParameter('createAtTo', $createAtTo);
        }

        if ($orderSearch->createdAtFrom) {
            $qb->andWhere('(o.createdAt >= :createDateFrom)')->setParameter('createDateFrom', $orderSearch->createdAtFrom);
        }

        if ($orderSearch->createdAtTo) {
            $qb->andWhere('(o.createdAt <= :createDateTo)')->setParameter('createDateTo', $orderSearch->createdAtTo);
        }

        $pagination = $orderSearch->pagination;
        $sort = $orderSearch->sort;

        /** @var Sort $sortItem */
        foreach ($sort as $sortItem) {
            $qb->addOrderBy(sprintf('o.%s', $sortItem->field), $sortItem->type);
        }

        return $this->paginate($qb, new PaginatedResult($pagination->page, $pagination->limit));
    }

    /**
     * @param OrderSearch $orderSearch
     * @return PaginatedResult
     */
    public function findList(OrderSearch $orderSearch): PaginatedResult
    {
        $qb = $this->createQueryBuilder('o');

        $qb->andWhere('o.projectName = :projectName')->setParameter('projectName', $orderSearch->projectName);
        $qb->andWhere('o.userRef = :userRef')->setParameter('userRef', $orderSearch->getUserRef());

        $pagination = $orderSearch->pagination;
        $sort = $orderSearch->sort;

        /** @var Sort $sortItem */
        foreach ($sort as $sortItem) {
            $qb->addOrderBy(sprintf('o.%s', $sortItem->field), $sortItem->type);
        }

        return $this->paginate($qb, new PaginatedResult($pagination->page, $pagination->limit));
    }

    /**
     * @param string $value
     * @param string|null $projectName
     * @return Order|null
     */
    public function findOneByHash(string $value, ?string $projectName): ?Order
    {
        return $this->findOneBy(
            array_merge(['hash' => $value], ($projectName !== null ? ['projectName' => $projectName] : []))
        );
    }

    public function findOneForUser(string $hash, string $projectName, ?string $userRef)
    {
        return $this->findOneBy(compact('hash', 'projectName', 'userRef'));
    }

    /**
     * @param string $value
     * @return Order|null
     */
    public function findOneByRef(string $value): ?Order
    {
        return $this->findOneBy(['ref' => $value]);
    }

    /**
     * @param string $phone
     * @param string $projectName
     * @param string $userRef
     * @return mixed
     */
    public function attachOrdersByPhone(string $phone, string $projectName, string $userRef)
    {
        $qb = $this->createQueryBuilder('o');

        $q = $qb->update()
            ->set('o.userRef', $qb->expr()->literal($userRef))
            ->andWhere('o.projectName = :projectName')
            ->andWhere('o.phone = :phone')
            ->andWhere('o.userRef IS NULL')
            ->setParameter('projectName', $projectName)
            ->setParameter('phone', $phone)
            ->getQuery();


        return $q->execute();
    }

    /**
     * @param string $projectName
     * @param string $slug
     * @param int $userRef
     * @return int
     */
    public function getCountByItemSlug(string $projectName, string $slug, int $userRef): int
    {
        $qb = $this->createQueryBuilder('o')
            ->select('count(o.id)')
            ->innerJoin('o.orderItems', 'i')
            ->where('o.userRef = :userRef')
            ->andWhere('o.projectName = :projectName')
            ->andWhere('i.slug = :slug')
            ->andWhere('o.status in(:status)')
            ->setParameter('userRef', $userRef)
            ->setParameter('projectName', $projectName)
            ->setParameter('slug', $slug)
            //TODO: Order::STATUS_COMPLETED пока не реализован, конечный статус заказа Order::STATUS_COMPLETED
            ->setParameter('status', [Order::STATUS_COMPLETED, Order::STATUS_TRANSIT]);

        try {
            return $qb->getQuery()->getSingleScalarResult();
        } catch (\Exception $exception) {
            return 0;
        }
    }
}
