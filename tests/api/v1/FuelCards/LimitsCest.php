<?php

namespace Tests\Api\V1\FuelCards;

use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class LimitsCest
{
    protected $basePath = '/api/v1/fuel-cards/{id}/limits';

    private function getUrl(string $id): string
    {
        return strtr($this->basePath, ['{id}' => $id]);
    }

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
    public function testSendGetHaveCardsReturnList(ApiTester $I, Example $example)
    {
        /** @var User $user */
        $user = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($user->getUserName());

        $client = $user->getCompany()->getClient();

        /** @var Driver $driver */
        $driver = $I->haveDriver($client);
        $carNumbers = [];
        foreach ($driver->getCarNumbers() as $carNumber) {
            $carNumbers[] = ['number' => $carNumber->getNumber()];
        }

        /** @var Card $card */
        $card = $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
            'status' => 'active',
        ], $driver);

        // fuel
        for ($i = 0; $i < 5; ++$i) {
            /** @var Type $fuelType */
            // fuel type
            $fuelType = $I->haveFuelType(['fuelType' => 1]);
            $collection[] = $I->haveFuelCardLimits([
                'client1CId' => $client->getClient1CId(),
                'cardNumber' => $card->getCardNumber(),
                'fuelCode' => $fuelType->getFuelCode(),
            ]);

            $I->haveCardTransaction([
                'client1CId' => $client->getClient1CId(),
                'cardNumber' => $card->getCardNumber(),
                'fuelCode' => $fuelType->getFuelCode(),
            ]);
        }

        $I->sendGET($this->getUrl($card->getId()));

        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'card' => [
                'id' => $card->getId(),
                'cardNumber' => $card->getCardNumber(),
                'status' => 'active',
                'driver' => [
                    'id' => $driver->getId(),
                    'firstName' => $driver->getName()->getFirstName(),
                    'lastName' => $driver->getName()->getLastName(),
                    'carsNumbers' => $carNumbers,
                ],
            ],
            'limits' => [
            ],
        ]);
    }

    public function testHaveBlockedCardReturnLimitsList(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->authorizeAsAdmin();

        $client = $user->getCompany()->getClient();

        /** @var Driver $driver */
        $driver = $I->haveDriver($client);
        $carNumbers = [];
        foreach ($driver->getCarNumbers() as $carNumber) {
            $carNumbers[] = ['number' => $carNumber->getNumber()];
        }

        $cardStatus = 'blocked';
        /** @var Card $card */
        $card = $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
            'status' => $cardStatus,
        ], $driver);

        // fuel
        for ($i = 0; $i < 5; ++$i) {
            /** @var Type $fuelType */
            // fuel type
            $fuelType = $I->haveFuelType(['fuelType' => 1]);
            $collection[] = $I->haveFuelCardLimits([
                'client1CId' => $client->getClient1CId(),
                'cardNumber' => $card->getCardNumber(),
                'fuelCode' => $fuelType->getFuelCode(),
            ]);

            $I->haveCardTransaction([
                'client1CId' => $client->getClient1CId(),
                'cardNumber' => $card->getCardNumber(),
                'fuelCode' => $fuelType->getFuelCode(),
            ]);
        }

        $I->sendGET($this->getUrl($card->getId()));

        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'card' => [
                'id' => $card->getId(),
                'cardNumber' => $card->getCardNumber(),
                'status' => $cardStatus,
                'driver' => [
                    'id' => $driver->getId(),
                    'firstName' => $driver->getName()->getFirstName(),
                    'lastName' => $driver->getName()->getLastName(),
                    'carsNumbers' => $carNumbers,
                ],
            ],
            'limits' => [
            ],
        ]);
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $user = $I->haveCabinetUser();

        $client = $user->getCompany()->getClient();

        /** @var Card $card */
        $card = $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
            'status' => 'active',
        ]);

        $I->sendGET($this->getUrl($card->getId()));

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }
}
