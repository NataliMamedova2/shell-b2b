<?php

namespace App\Api\Action\Api\V1\Documents\ListAction;

use App\Api\Resource\Document;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class DataTransformer implements \App\Api\Crud\Interfaces\DataTransformer
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param Pagerfanta $paginator
     *
     * @return array
     */
    public function transform($paginator)
    {
        if (!$paginator instanceof Pagerfanta) {
            throw new \InvalidArgumentException();
        }

        $collection = [];
        foreach ($paginator as $document) {
            $model = new Document($this->urlGenerator);
            $collection[] = $model->prepare($document);
        }

        return [
            'meta' => [
                'pagination' => [
                    'totalCount' => $paginator->getNbPages(),
                    'currentPage' => $paginator->getCurrentPage(),
                ],
            ],
            'data' => $collection,
        ];
    }
}
