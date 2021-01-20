<?php

namespace Tests\Api\V1\Transaction;

use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\Transaction\Card\Transaction;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class CardTransactionsListCest
{
    protected $basePath = '/api/v1/transactions/card';

    /**
     * Allowed roles.
     *
     * @param ApiTester $I
     * @param Example $example
     *
     * @throws \Exception
     *
     * @example { "role": "admin" }
     * @example { "role": "manager" }
     * @example { "role": "accountant" }
     */
    public function testGetCardTransactionsReturnEmptyList(ApiTester $I, Example $example)
    {
        $myself = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($myself->getUserName());

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'meta' => [
                'pagination' => [
                    'totalCount' => 'integer',
                    'currentPage' => 'integer',
                ],
                'accountBalance' => 'array|null',
            ],
            'data' => 'array',
        ]);

        $I->seeResponseEqualsJson([
            'meta' => [
                'pagination' => [
                    'totalCount' => 1,
                    'currentPage' => 1,
                ],
                'accountBalance' => null,
                'filters' => [
                    'supplies' => [],
                    'regions' => [],
                    'networkStations' => [],
                ],
            ],
            'data' => [],
        ]);
    }

    public function testGetCardTransactionsReturnTransactions(ApiTester $I)
    {
        $myself = $I->authorizeAsAdmin();

        $client = $myself->getCompany()->getClient();

        /** @var Type $fuel */
        $fuel = $I->haveFuelType(['fuelType' => 1]);

        /** @var Transaction $transaction_1 */
        $transaction_1 = $I->haveCardTransaction([
            'client1CId' => $client->getClient1CId(),
            'fuelCode' => $fuel->getFuelCode(),
            'type' => 0,
            'postDate' => new \DateTimeImmutable('2020-02-11 18:00:00'),
        ]);
        /** @var Transaction $transaction_2 */
        $transaction_2 = $I->haveCardTransaction([
            'client1CId' => $client->getClient1CId(),
            'fuelCode' => $fuel->getFuelCode(),
            'type' => 1,
            'postDate' => new \DateTimeImmutable('2020-02-11 17:00:00'),
        ]);
        /** @var Transaction $transaction_3 */
        $transaction_3 = $I->haveCardTransaction([
            'client1CId' => $client->getClient1CId(),
            'fuelCode' => $fuel->getFuelCode(),
            'type' => 2,
            'postDate' => new \DateTimeImmutable('2020-02-11 16:00:00'),
        ]);

        $I->haveClientInfo($client, ['balance' => -123.983]);

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'meta' => [
                'pagination' => [
                    'totalCount' => 'integer',
                    'currentPage' => 'integer',
                ],
                'accountBalance' => 'array|null',
                'filters' => [
                    'supplies' => 'array|null',
                    'regions' => 'array|null',
                    'networkStations' => 'array|null',
                ],
            ],
            'data' => 'array',
        ]);

        $I->seeResponseContainsJson([
            'meta' => [
                'pagination' => [
                    'totalCount' => 1,
                    'currentPage' => 1,
                ],
                'accountBalance' => [
                    'value' => 12398,
                    'sign' => '-',
                ],
                'filters' => [
                    'supplies' => [],
                    'regions' => [],
                    'networkStations' => [],
                ],
            ],
            'data' => [
                [
                    'id' => $transaction_1->getId(),
                    'cardNumber' => $transaction_1->getCardNumber(),
                    'fuelName' => $fuel->getFuelName(),
                    'volume' => $transaction_1->getFuelQuantity(),
                    'networkStation' => $transaction_1->getAzsName(),
                    'amount' => $transaction_1->getDebit(),
                    'price' => $transaction_1->getPrice(),
                    'status' => 'write-off',
                ],
                [
                    'id' => $transaction_2->getId(),
                    'cardNumber' => $transaction_2->getCardNumber(),
                    'fuelName' => $fuel->getFuelName(),
                    'volume' => $transaction_2->getFuelQuantity(),
                    'networkStation' => $transaction_2->getAzsName(),
                    'amount' => $transaction_2->getDebit(),
                    'price' => $transaction_2->getPrice(),
                    'status' => 'return',
                ],
                [
                    'id' => $transaction_3->getId(),
                    'cardNumber' => $transaction_3->getCardNumber(),
                    'fuelName' => $fuel->getFuelName(),
                    'volume' => $transaction_3->getFuelQuantity(),
                    'networkStation' => $transaction_3->getAzsName(),
                    'amount' => $transaction_3->getDebit(),
                    'price' => $transaction_3->getPrice(),
                    'status' => 'replenishment',
                ],
            ],
        ]);
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $I->haveCabinetUser();

        $I->sendGET($this->basePath);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }
}
