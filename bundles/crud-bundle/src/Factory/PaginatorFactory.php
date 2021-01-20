<?php

declare(strict_types=1);

namespace CrudBundle\Factory;

use Infrastructure\Criteria\CriteriaFactory;
use Infrastructure\Interfaces\Paginator\Paginator;
use Infrastructure\Paginator\PagerfantaPaginator;

final class PaginatorFactory
{
    public function __invoke(
        CriteriaFactory $criteriaFactory,
        string $entityClass
    ): Paginator {
        return new PagerfantaPaginator($criteriaFactory, $entityClass);
    }
}
