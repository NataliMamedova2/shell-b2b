<?php

namespace Tests\Api\V1\FuelCards;

use App\Application\Domain\ValueObject\ExportStatus;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\ValueObject\CardStatus;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\FuelLimit\ValueObject\PurseActivity;
use App\Clients\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class UpdateCest
{
    protected $basePath = '/api/v1/fuel-cards/update/{id}';

    private function getUrl(string $id): string
    {
        return strtr($this->basePath, ['{id}' => $id]);
    }

    public function testSendPostNoCardsReturnNotFound(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $I->sendPOST($this->getUrl('9773cbe9-d505-4537-af9e-9034882c6c08'), []);

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
    }

    public function testSendPostCardAnotherClientReturnNotFound(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        /** @var User $user */
        $user = $I->haveCabinetUser(['username' => 'qwerty']);
        $client = $user->getCompany()->getClient();

        $card = $I->haveFuelCard(['client1CId' => $client->getClient1CId()]);

        $I->sendPOST($this->getUrl($card->getId()), []);

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
    }

    public function testSendPOSTCardIsBlockedReturnNotFound(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->authorizeAsAdmin();
        $client = $user->getCompany()->getClient();

        /** @var Card $card */
        $card = $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
            'status' => 'blocked',
        ]);
        $I->sendPOST($this->getUrl($card->getId()), []);

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
    }

    public function testSendPOSTCardInStopListReturnNotFound(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->authorizeAsAdmin();
        $client = $user->getCompany()->getClient();

        /** @var Card $card */
        $card = $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
            'status' => 'active',
        ]);
        $I->haveStopList($card);

        $I->sendPOST($this->getUrl($card->getId()), []);

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
    }

    /**
     * Allowed roles.
     *
     * @param ApiTester $I
     * @param Example   $example
     *
     * @throws \Exception]
     *
     * @example { "role": "admin" }
     */
    public function testUpdateCardReturnCard(ApiTester $I, Example $example)
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

        /** @var Type $fuelType_1 */
        $fuelType_1 = $I->haveFuelType(['fuelType' => 1]);
        $I->haveFuelCardLimits([
            'client1CId' => $client->getClient1CId(),
            'cardNumber' => $card->getCardNumber(),
            'fuelCode' => $fuelType_1->getFuelCode(),
        ]);

        /** @var Type $fuelType */
        $fuelType = $I->haveFuelType(['fuelType' => 1]);
        $dayLimit = 50000;
        $weekLimit = 600000;
        $monthLimit = 700000;
        $timeUseFrom = '00:00';
        $timeUseTo = '23:59';

        $fuelDayLimit = 1000000;
        $fuelWeekLimit = 7000000;
        $fuelMonthLimit = 30000000;
        $data = [
            'totalLimits' => [
                'day' => $dayLimit,
                'week' => $weekLimit,
                'month' => $monthLimit,
            ],
            'startUseTime' => $timeUseFrom,
            'endUseTime' => $timeUseTo,
            'serviceDays' => ['mon', 'tue', 'wed', 'thu', 'fri', 'sun'],
            'fuelLimits' => [
                [
                    'id' => $fuelType->getId(),
                    'dayLimit' => $fuelDayLimit,
                    'weekLimit' => $fuelWeekLimit,
                    'monthLimit' => $fuelMonthLimit,
                ],
            ],
        ];
        $I->sendPOST($this->getUrl($card->getId()), $data);

        $I->seeResponseIsJson();
        $I->seeResponseContainsJson([
            'id' => $card->getId(),
            'onModeration' => true,
        ]);

        $I->seeInDatabase('cards', [
            'card_number' => $card->getCardNumber(),
            'client_1c_id' => $client->getClient1CId(),
            'day_limit' => $dayLimit * 100,
            'week_limit' => $weekLimit * 100,
            'month_limit' => $monthLimit * 100,
            'service_schedule' => '1111101',
            'time_use_from' => (new \DateTimeImmutable($timeUseFrom))->format('Y-m-d H:i:s'),
            'time_use_to' => (new \DateTimeImmutable($timeUseTo))->format('Y-m-d H:i:s'),
            'status' => CardStatus::active()->getValue(),
            'export_status' => ExportStatus::readyForExportStatus(),
        ]);

        $I->seeInDatabase('fuel_limits', [
            'card_number' => $card->getCardNumber(),
            'client_1c_id' => $client->getClient1CId(),
            'fuel_code' => $fuelType->getFuelCode(),
            'day_limit' => $fuelDayLimit * 100,
            'week_limit' => $fuelWeekLimit * 100,
            'month_limit' => $fuelMonthLimit * 100,
            'status' => PurseActivity::active()->getValue(),
            'export_status' => ExportStatus::readyForExportStatus(),
        ]);

        $I->seeInDatabase('fuel_limits', [
            'card_number' => $card->getCardNumber(),
            'client_1c_id' => $client->getClient1CId(),
            'fuel_code' => $fuelType_1->getFuelCode(),
            'export_status' => ExportStatus::readyForExportStatus(),
        ]);
    }

    public function testSendPOSTUpdateEmptyDataReturnErrors(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->authorizeAsAdmin();
        $client = $user->getCompany()->getClient();

        /** @var Card $card */
        $card = $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
            'status' => 'active',
        ]);
        $data = [];
        $I->sendPOST($this->getUrl($card->getId()), $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'errors' => 'array:!empty',
        ]);
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

        $data = [];
        $I->sendPOST($this->getUrl($card->getId()), $data);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }
}
