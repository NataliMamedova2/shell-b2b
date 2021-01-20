<?php

namespace Tests\Unit\Api\Action\Api\V1\Transactions\Card\CreateReportAction\Service;

use App\Api\Action\Api\V1\Transactions\Card\CreateReportAction\Service\GetReportTransactionsHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\Transaction\Card\Transaction;
use App\Clients\Domain\Transaction\Card\ValueObject\Type as TransactionType;
use App\Clients\Infrastructure\Fuel\Criteria\IndexByFuelCode;
use App\Clients\Infrastructure\Transaction\Repository\Repository as TransactionRepository;
use Infrastructure\Interfaces\Repository\Repository;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class GetReportTransactionsHandlerTest extends TestCase
{
    /**
     * @var TransactionRepository|ObjectProphecy
     */
    private $transactionRepositoryMock;
    /**
     * @var Repository|ObjectProphecy
     */
    private $fuelTypeRepositoryMock;
    /**
     * @var GetReportTransactionsHandler
     */
    private $handler;
    /**
     * @var QueryRequest|ObjectProphecy
     */
    private $queryRequestMock;

    protected function setUp(): void
    {
        $this->transactionRepositoryMock = $this->prophesize(TransactionRepository::class);
        $this->fuelTypeRepositoryMock = $this->prophesize(Repository::class);
        $this->queryRequestMock = $this->prophesize(QueryRequest::class);

        $this->handler = new GetReportTransactionsHandler(
            $this->transactionRepositoryMock->reveal(),
            $this->fuelTypeRepositoryMock->reveal()
        );
    }

    public function testHandleTransactionsNotFoundReturnArray(): void
    {
        $baseCriteria = [
            'type_equalTo' => TransactionType::return()->getName(),
        ];
        $this->queryRequestMock->getCriteria()
            ->shouldBeCalled()
            ->willReturn($baseCriteria);

        $order = ['createdAt' => 'ASC'];
        $this->queryRequestMock->getOrder()
            ->shouldBeCalled()
            ->willReturn($order);

        $supplies = ['type' => ['fuel']];
        $this->queryRequestMock->getQueryParams()
            ->shouldBeCalled()
            ->willReturn($supplies);

        // transactions
        $transactions = [];
        $typeCriteria = [
            'type_notEqualTo' => [
                TransactionType::replenishment()->getValue(),
            ],
        ];
        $criteria = array_merge($baseCriteria, $typeCriteria);
        $this->transactionRepositoryMock->findMany($criteria, $order)
            ->shouldBeCalled()
            ->willReturn($transactions);

        // replenishmentTransactions
        $replenishmentTransactions = [];
        $replenishmentCriteria = [
            'type_notIn' => [
                TransactionType::writeOff()->getValue(),
                TransactionType::return()->getValue(),
            ],
        ];
        $criteria = array_merge($baseCriteria, $replenishmentCriteria);
        $this->transactionRepositoryMock->findMany($criteria, $order)
            ->shouldBeCalled()
            ->willReturn($replenishmentTransactions);

        // fuelTypes
        $fuelTypes = [];
        $fuelCodes = [];
        $this->fuelTypeRepositoryMock->findMany([
            'fuelCode_in' => $fuelCodes,
            IndexByFuelCode::class => true,
        ])
            ->shouldBeCalled()
            ->willReturn($fuelTypes);

        $result = $this->handler->handle($this->queryRequestMock->reveal());

        $expectedResult = [
            'transactions' => $transactions,
            'replenishmentTransactions' => $replenishmentTransactions,
            'fuelTypes' => $fuelTypes,
        ];

        $this->assertEquals($expectedResult, $result);
    }

    public function testHandleTransactionsFoundReturnArray(): void
    {
        $baseCriteria = [
            'type_equalTo' => TransactionType::return()->getName(),
        ];
        $this->queryRequestMock->getCriteria()
            ->shouldBeCalled()
            ->willReturn($baseCriteria);

        $order = ['createdAt' => 'ASC'];
        $this->queryRequestMock->getOrder()
            ->shouldBeCalled()
            ->willReturn($order);

        // transactions
        $transaction_1_Mock = $this->prophesize(Transaction::class);
        $transactions = [$transaction_1_Mock->reveal()];
        $typeCriteria = [
            'type_notEqualTo' => [
                TransactionType::replenishment()->getValue(),
            ],
        ];
        $criteria = array_merge($baseCriteria, $typeCriteria);
        $this->transactionRepositoryMock->findMany($criteria, $order)
            ->shouldBeCalled()
            ->willReturn($transactions);

        // replenishmentTransactions
        $transaction_2_Mock = $this->prophesize(Transaction::class);
        $replenishmentTransactions = [$transaction_2_Mock->reveal()];
        $replenishmentCriteria = [
            'type_notIn' => [
                TransactionType::writeOff()->getValue(),
                TransactionType::return()->getValue(),
            ],
        ];
        $criteria = array_merge($baseCriteria, $replenishmentCriteria);
        $this->transactionRepositoryMock->findMany($criteria, $order)
            ->shouldBeCalled()
            ->willReturn($replenishmentTransactions);

        $fuelCode_1 = 'f-code_1';
        $transaction_1_Mock->getFuelCode()
            ->shouldBeCalled()
            ->willReturn($fuelCode_1);

        $fuelCode_2 = 'f-code_2';
        $transaction_2_Mock->getFuelCode()
            ->shouldBeCalled()
            ->willReturn($fuelCode_2);
        // fuelTypes
        $fuelTypeMock = $this->prophesize(Type::class);
        $fuelTypes = [$fuelTypeMock->reveal()];
        $fuelCodes = [$fuelCode_1, $fuelCode_2];
        $this->fuelTypeRepositoryMock->findMany([
            'fuelCode_in' => $fuelCodes,
            IndexByFuelCode::class => true,
        ])
            ->shouldBeCalled()
            ->willReturn($fuelTypes);

        $supplies = ['type' => ['fuel']];
        $this->queryRequestMock->getQueryParams()
            ->shouldBeCalled()
            ->willReturn($supplies);

        $result = $this->handler->handle($this->queryRequestMock->reveal());

        $expectedResult = [
            'transactions' => $transactions,
            'replenishmentTransactions' => $replenishmentTransactions,
            'fuelTypes' => $fuelTypes,
        ];

        $this->assertEquals($expectedResult, $result);
    }
}
