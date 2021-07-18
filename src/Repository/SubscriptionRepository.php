<?php

namespace App\Repository;

use App\DTO\PaginatedResult;
use App\DTO\Pagination;
use App\DTO\Subscription\AdminSubscriptionSearch;
use App\Entity\Card;
use App\Entity\Subscription;
use App\Traits\Repository\PaginateTrait;
use App\Traits\Repository\QueryLikeTrait;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Bridge\Doctrine\RegistryInterface;
use function Doctrine\ORM\QueryBuilder;

/**
 * @method Subscription|null find($id, $lockMode = null, $lockVersion = null)
 * @method Subscription|null findOneBy(array $criteria, array $orderBy = null)
 * @method Subscription[]    findAll()
 * @method Subscription[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionRepository extends ServiceEntityRepository
{
    use PaginateTrait;
    use QueryLikeTrait;

    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Subscription::class);
    }

    public function findListAdmin(AdminSubscriptionSearch $search, bool $isOnlyAvailableItems = false): PaginatedResult
    {
        $qb = $this->createQueryBuilder('s');

        if ($search->project !== null) {
            $qb->andWhere('s.project = :project')->setParameter('project', $search->project);
        }

        if ($search->userRef !== null) {
            $qb->andWhere('s.userRef = :userRef')->setParameter('userRef', $search->userRef);
        }

        if ($search->status !== null) {
            $qb->andWhere('s.status = :status')->setParameter('status', $search->status);
        }

        $this->addLikeIfNotNull($qb, 's.phone', $search->phone);
        $this->addLikeIfNotNull($qb, 's.firstName', $search->firstName);
        $this->addLikeIfNotNull($qb, 's.lastName', $search->lastName);

        $qb->innerJoin('s.subscriptionItems', 'si');

        if ($isOnlyAvailableItems) {
            $qb->select('s', 'si');
        }

        if ($search->isActive !== null) {
            $qb->andWhere('s.isActive = :isActive')->setParameter('isActive', $search->isActive);
            $qb->andWhere('si.isActive = :isActive')->setParameter('isActive', $search->isActive);
        }

        if (!empty($search->internalId)) {
            $qb->andWhere('si.internalId in (:internalId)')->setParameter('internalId', $search->internalId);
        }

        if ($search->productId !== null) {
            $qb->andWhere('si.productId = :productId')->setParameter('productId', $search->productId);
        }

        if ($search->dateFrom) {
            $qb->andWhere('si.startDate >= :startDateFrom')->setParameter('startDateFrom', $search->dateFrom);
        }

        if ($search->dateTo) {
            $qb->andWhere('si.startDate <= :startDateTo')->setParameter('startDateTo', $search->dateTo);
        }

        return $this->paginate($qb, new PaginatedResult($search->pagination->page, $search->pagination->limit));
    }

    public function findOneDefault(string $project, string $userRef): ?Subscription
    {
        return $this->findOneBy(
            [
                'project' => $project,
                'userRef' => $userRef,
                'isDefault' => true,
            ]
        );
    }

    public function updateStatusByCard(Card $card, int $status): void
    {
        $qb = $this->createQueryBuilder('s')
            ->update()
            ->set('s.status', ':status')->setParameter('status', $status)
            ->andWhere('s.card = :cart')->setParameter('cart', $card);

        $qb->getQuery()->execute();
    }

    public function findActiveForDate(DateTime $forDate, Pagination $pagination): PaginatedResult
    {
        $qb = $this->createQueryBuilder('s');

        $qb->andWhere('s.isActive = :isActive');
        $qb->andWhere('s.status = :status');
        $qb->innerJoin('s.subscriptionItems', 'si', Join::WITH, 'si.isActive = :isActive and si.startDate >= :forDate');

        $qb->andWhere(
            $qb->expr()->orX(
                $qb->expr()->andX(
                    $qb->expr()->isNull('si.skipDateFrom'),
                    $qb->expr()->isNull('si.skipDateTo')
                ),
                $qb->expr()->andX(
                    $qb->expr()->isNull('si.skipDateTo'),
                    $qb->expr()->isNotNull('si.skipDateFrom'),
                    $qb->expr()->gt('si.skipDateFrom', ':forDate')
                ),
                $qb->expr()->andX(
                    $qb->expr()->isNull('si.skipDateFrom'),
                    $qb->expr()->isNotNull('si.skipDateTo'),
                    $qb->expr()->lt('si.skipDateTo', ':forDate')
                ),
                $qb->expr()->andX(
                    $qb->expr()->isNotNull('si.skipDateFrom'),
                    $qb->expr()->isNotNull('si.skipDateTo'),
                    $qb->expr()->orX(
                        $qb->expr()->gt('si.skipDateFrom', ':forDate'),
                        $qb->expr()->lt('si.skipDateTo', ':forDate')
                    )
                )
            )
        );
        $qb->setParameter('forDate', $forDate);
        $qb->setParameter('status', Subscription::STATUS_APPROVED);
        $qb->setParameter('isActive', true);

        return $this->paginate($qb, new PaginatedResult($pagination->page, $pagination->limit));
    }
}
