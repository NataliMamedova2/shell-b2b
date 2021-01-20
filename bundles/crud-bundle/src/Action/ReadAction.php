<?php

namespace CrudBundle\Action;

use CrudBundle\Interfaces\ReadQueryRequest;
use Infrastructure\Interfaces\Repository\Repository;

final class ReadAction
{

    public function __invoke(ReadQueryRequest $readQueryRequest, Repository $repository): Response
    {
        $result = $repository->find(
            $readQueryRequest->getCriteria(),
            $readQueryRequest->getOrder()
        );

        return new Response([
            'result' => $result,
        ]);
    }
}
