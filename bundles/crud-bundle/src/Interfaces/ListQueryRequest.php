<?php

declare(strict_types=1);

namespace CrudBundle\Interfaces;

interface ListQueryRequest
{
    public function getCriteria(): array;

    public function getData(): array;

    public function getOrder(): array;

    public function getPage(): int;

    public function getLimit(): int;
}
