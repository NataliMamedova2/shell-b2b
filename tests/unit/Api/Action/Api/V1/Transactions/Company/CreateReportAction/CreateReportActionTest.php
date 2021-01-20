<?php

namespace Tests\Unit\Api\Action\Api\V1\Transactions\Company\CreateReportAction;

use App\Api\Action\Api\V1\Transactions\Company\CreateReportAction\CreateReportAction;
use App\Api\Action\Api\V1\Transactions\Company\CreateReportAction\Service\CreateExcelService;
use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Security\Cabinet\MyselfInterface;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class CreateReportActionTest extends TestCase
{
    /**
     * @var MyselfInterface|ObjectProphecy
     */
    private $myselfMock;
    /**
     * @var QueryRequest|ObjectProphecy
     */
    private $queryRequestMock;
    /**
     * @var QueryHandler|ObjectProphecy
     */
    private $queryHandlerMock;
    /**
     * @var CreateExcelService|ObjectProphecy
     */
    private $createExcelServiceMock;
    /**
     * @var CreateReportAction
     */
    private $action;


    protected function setUp(): void
    {
        $this->myselfMock = $this->prophesize(MyselfInterface::class);
        $this->queryRequestMock = $this->prophesize(QueryRequest::class);
        $this->queryHandlerMock = $this->prophesize(QueryHandler::class);

        $this->createExcelServiceMock = $this->prophesize(CreateExcelService::class);
        $this->repositoryMock = $this->prophesize(Repository::class);

        $this->action = new CreateReportAction($this->createExcelServiceMock->reveal(), $this->myselfMock->reveal());
    }

    public function testConstruct(): void
    {
        $result = new CreateReportAction($this->createExcelServiceMock->reveal(), $this->myselfMock->reveal());

        $this->assertInstanceOf(CreateReportAction::class, $result);
    }
}
