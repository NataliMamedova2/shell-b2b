<?php

namespace Tests\Api\V1\Invoice;

use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Domain\ShellInformation\ShellInformation;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class CreateFromAmountCest
{
    protected $basePath = '/api/v1/invoice/amount';

    /**
     * @var ShellInformation
     */
    private $shellInfo;

    public function _before(ApiTester $I)
    {
        $this->shellInfo = $I->haveShellInfo();

        /** @var Type $fuel */
        $fuel = $I->haveFuelType([
            'fuelType' => FuelType::fuel()->getValue(),
            'fuelCode' => 'КВЦ0000006',
        ]);
        $I->haveFuelPrice($fuel);
        /** @var Type $fuel */
        $fuel = $I->haveFuelType([
            'fuelType' => FuelType::fuel()->getValue(),
            'fuelCode' => 'КВЦ0000008',
        ]);
        $I->haveFuelPrice($fuel);
    }

    public function testSendPostEmptyDataReturnError(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $data = [];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'amount' => 'array:!empty',
        ], '$.errors');
    }

    public function testSendPosAmountIsEmptyReturnError(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $data = [
            'amount' => '',
        ];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'amount' => 'array:!empty',
        ], '$.errors');

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
    }

    public function testSendPostNegativeAmountReturnError(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $data = [
            'amount' => -10,
        ];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'amount' => 'array:!empty',
        ], '$.errors');
    }

    public function testSendPostAmountIsZeroReturnError(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $data = [
            'amount' => 0,
        ];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'amount' => 'array:!empty',
        ], '$.errors');
    }

    public function testSendPostAmountGreaterThanReturnError(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $maxValue = 1000000;
        $data = [
            'amount' => $maxValue + 1,
        ];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'amount' => 'array:!empty',
        ], '$.errors');
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
    public function testSendPostAmountValidReturnSuccess(ApiTester $I, Example $example)
    {
        $myself = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($myself->getUserName());

        $client = $myself->getCompany()->getClient();

        $data = [
            'amount' => 1000,
        ];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'name' => 'string:!empty',
            'link' => 'string:!empty',
        ]);

        $invoiceId = $I->grabColumnFromDatabase('invoices', 'id', [
            'client_1c_id' => $client->getClient1CId(),
            'creation_date' => date('Y-m-d'),
        ]);
        $id = isset($invoiceId[0]) ? $invoiceId[0] : 0;

        $I->seeInDatabase('invoices', [
            'client_1c_id' => $client->getClient1CId(),
            'value_tax' => $this->shellInfo->getValueAddedTax(),
            'creation_date' => date('Y-m-d'),
            'export_status' => 1,
        ]);

        $I->seeNumRecords(2, 'invoices_items', [
            'invoice_id' => $id,
        ]);
    }

    public function testSendPostMinAmountReturnSuccess(ApiTester $I)
    {
        $user = $I->authorizeAsAdmin();
        $client = $user->getCompany()->getClient();

        $data = [
            'amount' => 100,
        ];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'name' => 'string:!empty',
            'link' => 'string:!empty',
        ]);

        $I->seeInDatabase('invoices', [
            'client_1c_id' => $client->getClient1CId(),
            'creation_date' => date('Y-m-d'),
        ]);
    }

    public function testSendPostMaxAmountReturnSuccess(ApiTester $I)
    {
        $user = $I->authorizeAsAdmin();
        $client = $user->getCompany()->getClient();

        $data = [
            'amount' => 1000000,
        ];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'name' => 'string:!empty',
            'link' => 'string:!empty',
        ]);

        $I->seeInDatabase('invoices', [
            'client_1c_id' => $client->getClient1CId(),
            'creation_date' => date('Y-m-d'),
        ]);
    }

    public function testUnauthorizedReturn401(ApiTester $I)
    {
        $I->haveCabinetUser();

        $I->sendPOST($this->basePath);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED);
        $I->seeResponseIsJson();
    }
}
