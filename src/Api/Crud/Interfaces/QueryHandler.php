<?php

namespace App\Api\Crud\Interfaces;

interface QueryHandler
{
    public function handle(QueryRequest $queryRequest);
}
