<?php

namespace CrudBundle;

use Symfony\Component\HttpFoundation\Request;

final class ReadQueryRequest implements Interfaces\ReadQueryRequest
{
    /**
     * @var Request|null
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getCriteria(): array
    {
        return ['id_equalTo' => $this->request->get('id')];
    }

    public function getOrder(): array
    {
        return [];
    }
}
