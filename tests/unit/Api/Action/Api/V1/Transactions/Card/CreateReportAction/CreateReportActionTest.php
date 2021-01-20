<?php

namespace Tests\Unit\Api\Action\Api\V1\Transactions\Card\CreateReportAction;

use App\Api\Action\Api\V1\Transactions\Card\CreateReportAction\CreateReportAction;
use App\Api\Action\Api\V1\Transactions\Card\CreateReportAction\Service\CreateExcelService;
use App\Api\Action\Api\V1\Transactions\Card\CreateReportAction\Service\TransactionsXlsWriter;
use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Company\Company;
use App\Security\Cabinet\MyselfInterface;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\Unit\Clients\Domain\Fuel\Type\TypeTest;
use Tests\Unit\Clients\Domain\Transaction\Card\TransactionTest;

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

        $this->createExcelServiceMock = $this->prophesize(TransactionsXlsWriter::class);

        $this->action = new CreateReportAction($this->createExcelServiceMock->reveal(), $this->myselfMock->reveal());
    }

    public function testConstruct(): void
    {
        $result = new CreateReportAction($this->createExcelServiceMock->reveal(), $this->myselfMock->reveal());

        $this->assertInstanceOf(CreateReportAction::class, $result);
    }

    public function testInvokeHandlerReturnEmptyArrayReturnStreamedResponse(): void
    {
        $this->queryHandlerMock->handle($this->queryRequestMock->reveal())
            ->shouldBeCalled()
            ->willReturn([]);

        $client = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($client);

        $company = 'test';
        $client->getFullName()
            ->shouldBeCalled()
            ->willReturn($company);

        $queryRequestParams = [];
        $this->queryRequestMock->getQueryParams()
            ->shouldBeCalled()
            ->willReturn($queryRequestParams);

        $writer = $this->prophesize(Xls::class);
        $this->createExcelServiceMock->create([], [], [], $company, $queryRequestParams)
            ->shouldBeCalled()
            ->willReturn($writer);

        $result = $this->action->__invoke($this->queryRequestMock->reveal(), $this->queryHandlerMock->reveal());

        $this->assertInstanceOf(StreamedResponse::class, $result);
    }

    public function testInvokeHaveTransactionsReturnStreamedResponse(): void
    {
        $transactions = [TransactionTest::createValidEntity()];
        $replenishmentTransactions = [TransactionTest::createValidEntity()];
        $fuelTypes = [TypeTest::createValidEntity()];
        $data = [
            'transactions' => $transactions,
            'replenishmentTransactions' => $replenishmentTransactions,
            'fuelTypes' => $fuelTypes,
        ];
        $this->queryHandlerMock->handle($this->queryRequestMock->reveal())
            ->shouldBeCalled()
            ->willReturn($data);

        $client = $this->prophesize(Client::class);
        $this->myselfMock->getClient()
            ->shouldBeCalled()
            ->willReturn($client);

        $company = 'test';
        $client->getFullName()
            ->shouldBeCalled()
            ->willReturn($company);

        $queryRequestParams = [
            'dateFrom' => '2020-01-01',
            'dateTo' => '2020-02-01',
            'cardNumber' => '',
            'suppliesCodes' => [],
            'status' => '',
        ];
        $this->queryRequestMock->getQueryParams()
            ->shouldBeCalled()
            ->willReturn($queryRequestParams);

        $writer = $this->prophesize(Xls::class);
        $this->createExcelServiceMock->create($transactions, $replenishmentTransactions, $fuelTypes, $company, $queryRequestParams)
            ->shouldBeCalled()
            ->willReturn($writer);

        $result = $this->action->__invoke($this->queryRequestMock->reveal(), $this->queryHandlerMock->reveal());

        $this->assertInstanceOf(StreamedResponse::class, $result);
    }
}
