<?php

namespace App\Clients\Action\Backend\Transaction\CreateReportAction;

use App\Clients\Action\Backend\Transaction\ListAction\QueryRequest as ListQueryRequest;
use App\Api\Crud\Interfaces\QueryRequest;

final class QueryRequestAdapter implements QueryRequest
{
    /**
     * @var ListQueryRequest
     */
    protected $queryRequest;

    public function __construct(ListQueryRequest $queryRequest)
    {
        $this->queryRequest = $queryRequest;
    }

    public function getCriteria(): array
    {
        return $this->queryRequest->getCriteria();
    }
    public function getOrder(): array
    {
        return $this->queryRequest->getOrder();
    }
    public function getQueryParams(): array
    {
        $data = $this->queryRequest->getData();

        if (!isset($data['dateTo']) || empty($data['dateTo'])) {
            $data['dateTo'] = date("Y-m-d");
        }

        return $data;
    }
}
