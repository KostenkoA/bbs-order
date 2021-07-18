<?php

namespace App\Repository;

use App\Entity\Card;
use Doctrine\ORM\EntityRepository;

/**
 * Class CardRepository
 * @method Card|null findOneBy(array $criteria, array $orderBy = null)
 * @method Card|null find($id, $lockMode = null, $lockVersion = null)
 * @method Card[]    findAll()
 * @method Card[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 * @package App\Repository
 */
class CardRepository extends EntityRepository
{
    public function findByHash(string $project, string $userRef, string $hash): ?Card
    {
        return $this->findOneBy(compact('project', 'userRef', 'hash'));
    }
}
