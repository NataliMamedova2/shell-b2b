<?php

namespace App\Api\Action\Api\V1\Users\ReadAction;

use App\Clients\Domain\User\ValueObject\Role;
use App\Clients\Domain\User\ValueObject\Status;
use App\Security\Cabinet\Myself;
use Symfony\Component\HttpFoundation\Request;

final class QueryRequest implements \App\Api\Crud\Interfaces\QueryRequest
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var Myself
     */
    private $myself;

    public function __construct(Request $request, Myself $myself)
    {
        $this->request = $request;
        $this->myself = $myself;
    }

    public function getCriteria(): array
    {
        $myself = $this->myself->get();
        $company = $myself->getCompany();
        $criteria = [
            'id_equalTo' => $this->request->get('id'),
            'company_equalTo' => $company,
            'id_notEqualTo' => $myself->getId(),
        ];

        return $criteria;
    }

    public function getOrder(): array
    {
        return [];
    }

    public function getQueryParams(): array
    {
        return [];
    }
}
