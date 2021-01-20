<?php

namespace App\Api\Crud\Action\QueryRequest;

use App\Api\Crud\Interfaces\QueryRequest;
use Symfony\Component\HttpFoundation\Request;

class ListQueryRequest implements QueryRequest
{
    /**
     * @var Request
     */
    private $request;

    protected $defaultLimit = 100;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function getRequest(): Request
    {
        return $this->request;
    }

    public function getCriteria(): array
    {
        return [];
    }

    public function getOrder(): array
    {
        return [];
    }

    public function getQueryParams(): array
    {
        return [
            'limit' => (int) $this->request->get('limit', $this->defaultLimit),
            'offset' => (int) $this->request->get('offset', 0),
        ];
    }
}
