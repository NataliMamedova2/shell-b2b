<?php

namespace App\Users\Action\Backend\ListAction;

use CrudBundle\Action\ListAction\QueryRequest as BaseQueryRequest;
use CrudBundle\Interfaces\ListQueryRequest;

final class QueryRequest extends BaseQueryRequest implements ListQueryRequest
{
    public function getOrder(): array
    {
        return ['createdAt' => 'ASC'];
    }
}
