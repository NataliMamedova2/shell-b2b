<?php

namespace Tests\Api\V1\FuelCards;

use App\Application\Domain\ValueObject\ExportStatus;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\ValueObject\CardStatus;
use App\Clients\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class StopListCest
{
    protected $basePath = '/api/v1/fuel-cards/stop-list/{id}/add';

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
    public function testSendPostAddToStopListReturnTrue(ApiTester $I, Example $example)
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

        $I->sendPOST($this->getUrl($card->getId()));

        $I->seeResponseIsJson();

        $I->seeResponseContainsJson([
            'success' => true,
        ]);

        $I->seeInDatabase('cards', [
            'card_number' => $card->getCardNumber(),
            'status' => CardStatus::blocked()->getValue(),
            'export_status' => ExportStatus::new()->getStatus(),
        ]);
        $I->seeInDatabase('cards_stop_list', [
            'card_number' => $card->getCardNumber(),
            'export_status' => ExportStatus::readyForExportStatus(),
        ]);
    }

    public function testSendPostNotMyCardReturnException(ApiTester $I)
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

        $I->sendPOST($this->getUrl($card->getId()));

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
    }

    public function testSendPostCardAnotherClientReturnException(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $client = $I->haveClient();

        /** @var Card $card */
        $card = $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
            'status' => 'blocked',
        ]);

        $I->sendPOST($this->getUrl($card->getId()));

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
    }

    public function testSendPostCardAlreadyBlockedReturnException(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->authorizeAsAdmin();
        $client = $user->getCompany()->getClient();

        /** @var Card $card */
        $card = $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
            'status' => 'blocked',
        ]);

        $I->sendPOST($this->getUrl($card->getId()));

        $I->seeResponseCodeIs(404);
        $I->seeResponseIsJson();
    }

    public function testSendPostActiveCardInStopListReturnOk(ApiTester $I)
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

        $I->sendPOST($this->getUrl($card->getId()));

        $I->seeResponseIsJson();

        $I->seeInDatabase('cards', [
            'card_number' => $card->getCardNumber(),
            'status' => CardStatus::blocked()->getValue(),
        ]);
    }

    public function testSendPostCardAlreadyInStopInProgressListReturnOk(ApiTester $I)
    {
        /** @var User $user */
        $user = $I->authorizeAsAdmin();
        $client = $user->getCompany()->getClient();

        /** @var Card $card */
        $card = $I->haveFuelCard([
            'client1CId' => $client->getClient1CId(),
            'status' => 'active',
        ]);

        $I->haveStopListInProgressStatus($card);

        $I->sendPOST($this->getUrl($card->getId()));

        $I->seeResponseIsJson();

        $I->seeInDatabase('cards', [
            'card_number' => $card->getCardNumber(),
            'status' => CardStatus::blocked()->getValue(),
        ]);

        $inProgressStatus = 2;
        $I->seeInDatabase('cards_stop_list', [
            'card_number' => $card->getCardNumber(),
            'export_status' => $inProgressStatus,
        ]);
    }

    public function testSendPostNoIdReturnException(ApiTester $I)
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

        $I->sendPOST($this->getUrl(''));

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
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
        $I->sendPOST($this->getUrl($card->getId()));

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }
}
