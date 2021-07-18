<?php

namespace App\Traits\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;

trait QueryResultCountTrait
{
    /**
     * @param QueryBuilder $queryBuilder
     * @param $alias
     * @param string $countField
     * @return int
     */
    protected function getQueryResultCount(QueryBuilder $queryBuilder, $alias, string $countField = 'id'): int
    {
        try {
            $count = $queryBuilder->select(
                $countField ? sprintf('COUNT(DISTINCT(%s.%s))', $alias, $countField) : 'COUNT(1)'
            )->getQuery()->getSingleScalarResult();
        } catch (NonUniqueResultException $e) {
            $count = 0;
        }

        return $count;
    }
}
