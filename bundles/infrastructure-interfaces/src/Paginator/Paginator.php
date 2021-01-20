<?php

declare(strict_types=1);

namespace Infrastructure\Interfaces\Paginator;

interface Paginator
{
    public function paginate($criteria = null, array $order = null, int $page = 1, int $limit = 10);
}
