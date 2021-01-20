<?php

namespace App\Api\Action\Api\V1\Transactions\Company\CreateReportAction;

use App\Api\Action\Api\V1\Transactions\Company\ListAction\QueryRequest as ListQueryRequest;

final class QueryRequest extends ListQueryRequest
{
    public function getCriteria(): array
    {
        $criteria = parent::getCriteria();

        if (null === $this->request->get('dateFrom') && null === $this->request->get('dateTo')) {
            $date = new \DateTime('-1 month');
            $date->setTime(0, 0, 0);
            $criteria['date_greaterThanOrEqualTo'] = $date;
        }

        if (null !== $this->request->get('dateFrom')) {
            $date = \DateTime::createFromFormat('Y-m-d', $this->request->get('dateFrom'));
            $date->setTime(1, 0, 0);
            $criteria['date_greaterThanOrEqualTo'] = $date;
        }

        if (null !== $this->request->get('dateTo')) {
            $date = \DateTime::createFromFormat('Y-m-d', $this->request->get('dateTo'));
            $date->setTime(23, 0, 0);
            $criteria['date_lessThanOrEqualTo'] = $date;
        }

        if (null !== $this->request->get('type') && false === empty($this->request->get('type'))) {
            $type = $this->request->get('type');

            $criteria['type_equalTo'] = $type;
        }

        return $criteria;
    }

    public function getQueryParams(): array
    {
        $defaultDateFrom = date('Y-m-d', strtotime('-1 month'));
        $defaultDateTo = date('Y-m-d');

        return [
            'dateFrom' => $this->request->get('dateFrom', $defaultDateFrom),
            'dateTo' => $this->request->get('dateTo', $defaultDateTo),
            'type' => $this->request->get('type', ''),
        ];
    }
}
