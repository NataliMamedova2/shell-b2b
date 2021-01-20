<?php

namespace App\Api\Crud;

use App\Api\Crud\Interfaces\QueryRequest;
use Symfony\Component\HttpFoundation\Request;

final class DefaultQueryRequest implements QueryRequest
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getCriteria(): array
    {
        return [];
    }

    public function getOrder(): array
    {
        return ['createdAt' => 'DESC'];
    }

    public function getQueryParams(): array
    {
        $data = $this->request->query->all();

        return array_diff($data, ['']);
    }
}
