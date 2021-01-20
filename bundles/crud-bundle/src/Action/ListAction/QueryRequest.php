<?php

namespace CrudBundle\Action\ListAction;

use CrudBundle\Interfaces\ListQueryRequest;
use Symfony\Component\HttpFoundation\Request;

class QueryRequest implements ListQueryRequest
{
    private const ITEMS_ON_PAGE = 25;

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
        return [];
    }

    public function getPage(): int
    {
        return $this->request->get('page', 1);
    }

    public function getLimit(): int
    {
        return $this->request->get('limit', self::ITEMS_ON_PAGE);
    }

    public function getData(): array
    {
        return $this->getRequest()->query->all();
    }

    /**
     * @return Request
     */
    protected function getRequest(): Request
    {
        return $this->request;
    }
}
