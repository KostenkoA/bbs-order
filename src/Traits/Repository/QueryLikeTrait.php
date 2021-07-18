<?php

namespace App\Traits\Repository;

use Doctrine\ORM\QueryBuilder;

trait QueryLikeTrait
{
    /**
     * @param QueryBuilder $qb
     * @param string $field
     * @param string|null $value
     * @return QueryBuilder
     */
    private function addLikeIfNotNull(QueryBuilder $qb, string $field, ?string $value): QueryBuilder
    {
        if ($value !== null) {
            list($alias, $fieldName) = explode('.', $field);
            $qb->andWhere(sprintf('%s LIKE :%s', $field, $fieldName));
            $qb->setParameter($fieldName, sprintf('%%%s%%', $value));
        }

        return $qb;
    }
}
