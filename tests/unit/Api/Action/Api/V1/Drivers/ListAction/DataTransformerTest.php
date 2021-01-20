<?php

namespace Tests\Unit\Api\Action\Api\V1\Drivers\ListAction;

use App\Api\Action\Api\V1\Drivers\ListAction\DataTransformer;
use Pagerfanta\Pagerfanta;
use PHPUnit\Framework\TestCase;

final class DataTransformerTest extends TestCase
{
    public function testTransformNotPagerfantaReturnException()
    {
        $dataTransformer = new DataTransformer();

        $this->expectException(\InvalidArgumentException::class);
        $dataTransformer->transform(null);
    }

    public function testTransformPagerfantaReturnArray()
    {
        $pagerfantaMock = $this->prophesize(Pagerfanta::class);

        $nbPagesValue = 2;
        $pagerfantaMock->getNbPages()
            ->shouldBeCalled()
            ->willReturn($nbPagesValue);
        $curentPageValue = 1;
        $pagerfantaMock->getCurrentPage()
            ->shouldBeCalled()
            ->willReturn($curentPageValue);

        $iterator = [];
        $pagerfantaMock->getCurrentPageResults()
            ->shouldBeCalled()
            ->willReturn($iterator);

        $dataTransformer = new DataTransformer();

        $result = $dataTransformer->transform($pagerfantaMock->reveal());

        $expected = [
            'meta' => [
                'pagination' => [
                    'totalCount' => $nbPagesValue,
                    'currentPage' => $curentPageValue,
                ],
            ],
            'data' => [],
        ];

        $this->assertEquals($expected, $result);
    }
}
