<?php

namespace App\Api\Action\Api\V1\Transactions\Card\CreateReportAction;

use \App\Api\Action\Api\V1\Transactions\Card\ListAction\QueryRequest as ListQueryRequest;

final class QueryRequest extends ListQueryRequest
{
    public function getCriteria(): array
    {
        $criteria = parent::getCriteria();

        if (null === $this->request->get('dateFrom') && null === $this->request->get('dateTo')) {
            $date = new \DateTime("-1 month");
            $date->setTime(0, 0, 0);
            $criteria['postDate_greaterThanOrEqualTo'] = $date;
        }

        return $criteria;
    }

    public function getQueryParams(): array
    {
        $defaultDateFrom = date("Y-m-d", strtotime("-1 month"));
        $defaultDateTo = date("Y-m-d");

        return [
            'dateFrom' => $this->request->get('dateFrom', $defaultDateFrom),
            'dateTo' => $this->request->get('dateTo', $defaultDateTo),
            'cardNumber' => $this->request->get('cardNumber', ''),
            'suppliesCodes' => $this->request->get('supplies', []),
            'status' => $this->request->get('status', ''),
        ];
    }
}
