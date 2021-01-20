<?php

namespace App\Api\Crud\Interfaces;

interface QueryRequest
{
    public function getCriteria(): array;
    public function getOrder(): array;
    public function getQueryParams(): array;
}
