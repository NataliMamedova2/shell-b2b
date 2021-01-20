<?php

namespace Tests\Api\V1\FuelCards;

use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ChangeDriverCest
{
    protected $basePath = '/api/v1/fuel-cards/change-driver/{id}';

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
    public function testChangeDriverMyCardReturnTrue(ApiTester $I, Example $example)
    {
        /** @var User $user */
        $user = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($user->getUserName());

        $client = $user->getCompany()->getClient();

        /** @var Card $card */
        $card = $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
            'status' => 'active',
        ]);

        /** @var Driver $driver */
        $driver = $I->haveDriver($client);
        $carNumbers = [];
        foreach ($driver->getCarNumbers() as $carNumber) {
            $carNumbers[] = ['number' => $carNumber->getNumber()];
        }

        $data = [
            'driverId' => $driver->getId(),
        ];

        $I->sendPOST($this->getUrl($card->getId()), $data);

        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'id' => $driver->getId(),
            'firstName' => $driver->getName()->getFirstName(),
            'lastName' => $driver->getName()->getLastName(),
            'carsNumbers' => $carNumbers,
        ]);

        $I->seeInDatabase('cards', [
            'card_number' => $card->getCardNumber(),
            'driver_id' => $driver->getId(),
        ]);
    }

    public function testChangeDriverNotMyCardReturnException(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        /** @var User $user */
        $user = $I->haveCabinetUser();
        $client = $user->getCompany()->getClient();

        /** @var Card $card */
        $card = $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
            'status' => 'active',
        ]);

        /** @var Driver $driver */
        $driver = $I->haveDriver($client);

        $data = [
            'driverId' => $driver->getId(),
        ];

        $I->sendPOST($this->getUrl($card->getId()), $data);

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
    }

    public function testChangeDriverBlockedCardReturnException(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->authorizeAsAdmin();
        $client = $user->getCompany()->getClient();

        /** @var Card $card */
        $card = $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
            'status' => 'blocked',
        ]);

        /** @var Driver $driver */
        $driver = $I->haveDriver($client);

        $data = [
            'driverId' => $driver->getId(),
        ];

        $I->sendPOST($this->getUrl($card->getId()), $data);

        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
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
        $I->sendPOST($this->getUrl($card->getId()), []);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }
}
