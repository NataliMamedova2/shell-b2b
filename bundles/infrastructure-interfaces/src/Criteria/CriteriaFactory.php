<?php

declare(strict_types=1);

namespace Infrastructure\Interfaces\Criteria;

interface CriteriaFactory
{
    public function build($entityName, array $filter = [], $alias = 'e'): Criteria;
}
