<?php

declare(strict_types=1);

namespace CrudBundle\Interfaces;

interface ReadQueryRequest
{
    public function getCriteria(): array;

    public function getOrder(): array;
}
