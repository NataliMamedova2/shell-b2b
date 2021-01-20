<?php

namespace Tests\Api\V1\FuelCards;

use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\ValueObject\CardStatus;
use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\FuelLimit\ValueObject\PurseActivity;
use App\Clients\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ReadCest
{
    protected $basePath = '/api/v1/fuel-cards';

    public function testSendGetNoCardsReturnNotFound(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $I->sendGET("$this->basePath/9773cbe9-d505-4537-af9e-9034882c6c08");

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
    }

    public function testSendGetCardAnotherClientReturnNotFound(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        /** @var User $user */
        $user = $I->haveCabinetUser();
        $client = $user->getCompany()->getClient();

        $card = $I->haveFuelCard(['client1CId' => $client->getClient1CId()]);
        $I->sendGET("$this->basePath/{$card->getId()}");

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
    }

    public function testSendGetCardIsBlockedReturnNotFound(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->authorizeAsAdmin();
        $client = $user->getCompany()->getClient();

        /** @var Card $card */
        $card = $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
            'status' => 'blocked',
        ]);
        $I->sendGET("$this->basePath/{$card->getId()}");

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
    }

    public function testSendGetCardInStopListReturnNotFound(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->authorizeAsAdmin();
        $client = $user->getCompany()->getClient();

        /** @var Card $card */
        $card = $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
            'serviceSchedule' => '1010011',
            'status' => 'active',
        ]);
        $I->haveStopList($card);

        $I->sendGET("$this->basePath/{$card->getId()}");

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
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
    public function testSendGetHaveActiveCardReturnCard(ApiTester $I, Example $example)
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
            'serviceSchedule' => '1010011',
            'status' => 'active',
        ], $driver);

        $I->sendGET("$this->basePath/{$card->getId()}");

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'id' => $card->getId(),
            'cardNumber' => $card->getCardNumber(),
            'status' => (new CardStatus($card->getStatus()))->getName(),
            'onModeration' => false,
            'driver' => [
                'id' => $driver->getId(),
                'firstName' => $driver->getName()->getFirstName(),
                'lastName' => $driver->getName()->getLastName(),
                'carsNumbers' => $carNumbers,
            ],
            'totalLimits' => [
                'day' => $card->getDayLimit(),
                'week' => $card->getWeekLimit(),
                'month' => $card->getMonthLimit(),
            ],
            'serviceDays' => [
                'mon',
                'wed',
                'sat',
                'sun',
            ],
            'fuelLimits' => [],
            'goodsLimits' => [],
            'servicesLimits' => [],
        ]);

        $I->seeResponseMatchesJsonType([
            'id' => 'string:!empty',
            'cardNumber' => 'string:!empty',
            'onModeration' => 'boolean',
            'status' => 'string:enum(["active", "blocked"])',
            'totalLimits' => 'array',
            'startUseTime' => 'string:!empty',
            'endUseTime' => 'string:!empty',
            'serviceDays' => 'array',
            'fuelLimits' => 'array',
            'goodsLimits' => 'array',
            'servicesLimits' => 'array',
        ]);
    }

    public function testSendGetCardWithLimitsReturnCard(ApiTester $I)
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
            $I->haveFuelCardLimits([
                'client1CId' => $client->getClient1CId(),
                'cardNumber' => $card->getCardNumber(),
                'fuelCode' => $fuelType->getFuelCode(),
            ]);
        }
        // goods
        for ($i = 0; $i < 3; ++$i) {
            /** @var Type $fuelType */
            // goods type
            $fuelType = $I->haveFuelType(['fuelType' => 2]);
            $I->haveFuelCardLimits([
                'client1CId' => $client->getClient1CId(),
                'cardNumber' => $card->getCardNumber(),
                'fuelCode' => $fuelType->getFuelCode(),
            ]);
        }
        // services
        for ($i = 0; $i < 2; ++$i) {
            /** @var Type $fuelType */
            // services type
            $fuelType = $I->haveFuelType(['fuelType' => 3]);
            $I->haveFuelCardLimits([
                'client1CId' => $client->getClient1CId(),
                'cardNumber' => $card->getCardNumber(),
                'fuelCode' => $fuelType->getFuelCode(),
            ]);
        }

        $I->sendGET("$this->basePath/{$card->getId()}");

        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'id' => $card->getId(),
            'cardNumber' => $card->getCardNumber(),
            'status' => 'active',
            'driver' => [
                'id' => $driver->getId(),
                'firstName' => $driver->getName()->getFirstName(),
                'lastName' => $driver->getName()->getLastName(),
                'carsNumbers' => $carNumbers,
            ],
        ]);

        $I->seeResponseMatchesJsonType([
            'id' => 'string:!empty',
            'name' => 'string:!empty',
            'dayLimit' => 'integer:!empty',
            'weekLimit' => 'integer:!empty',
            'monthLimit' => 'integer:!empty',
        ], '$.fuelLimits[0]');

        $I->seeResponseMatchesJsonType([
            'id' => 'string:!empty',
            'name' => 'string:!empty',
            'dayLimit' => 'integer:!empty',
            'weekLimit' => 'integer:!empty',
            'monthLimit' => 'integer:!empty',
        ], '$.goodsLimits[0]');

        $I->seeResponseMatchesJsonType([
            'id' => 'string:!empty',
            'name' => 'string:!empty',
            'dayLimit' => 'integer:!empty',
            'weekLimit' => 'integer:!empty',
            'monthLimit' => 'integer:!empty',
        ], '$.servicesLimits[0]');
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->haveCabinetUser();
        $client = $user->getCompany()->getClient();

        /** @var Card $card */
        $card = $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
        ]);
        $I->sendGET("$this->basePath/{$card->getId()}");

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }
}
