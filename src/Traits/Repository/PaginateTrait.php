<?php


namespace App\Traits\Repository;

use App\DTO\PaginatedResult;
use App\DTO\Repository\ListResult;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;

trait PaginateTrait
{
    use QueryResultCountTrait;

    protected function defaultPagination(): PaginatedResult
    {
        return new PaginatedResult(1, 20);
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param PaginatedResult $resultModel
     * @param string $countField
     * @return PaginatedResult
     */
    protected function paginate(
        QueryBuilder $queryBuilder,
        PaginatedResult $resultModel,
        string $countField = 'id'
    ): PaginatedResult {
        $aliases = $queryBuilder->getRootAliases();
        $alias = array_shift($aliases);

        $count = $this->getQueryResultCount((clone $queryBuilder), $alias, $countField);

        $queryBuilder->setFirstResult($resultModel->getFirstResult());
        $queryBuilder->setMaxResults($resultModel->limit);

//        TODO: temporary commented
//        if ($countField) {
//            $queryBuilder->groupBy(sprintf('%s.%s', $alias, $countField));
//        }

        $resultModel->count = $count;
        $resultModel->items = $queryBuilder->getQuery()->getResult();

        return $resultModel;
    }
}
