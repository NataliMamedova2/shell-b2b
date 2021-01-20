<?php

namespace Tests\Api\V1\Invoice;

use App\Clients\Domain\Fuel\Price\Price;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\ShellInformation\ShellInformation;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class CreateFromSuppliesCest
{
    protected $basePath = '/api/v1/invoice/supplies';

    /**
     * @var ShellInformation
     */
    private $shellInfo;

    public function _before(ApiTester $I)
    {
        $this->shellInfo = $I->haveShellInfo();
    }

    public function testSendPostEmptyDataReturnError(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        $data = [];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'items' => 'array:!empty',
        ], '$.errors');
    }

    public function testSendPostVolumeIsEmptyReturnError(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        /** @var Type $fuel */
        $fuel = $I->haveFuelType(['fuelType' => 1]);
        $I->haveFuelPrice($fuel);

        $data = [
            'items' => [
                [
                    'id' => $fuel->getId(),
                    'volume' => 0,
                ],
            ],
        ];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'volume' => 'array:!empty',
        ], '$.errors.items[0]');
    }

    public function testSendPostVolumeIsNegativeReturnError(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        /** @var Type $fuel */
        $fuel = $I->haveFuelType(['fuelType' => 1]);
        $I->haveFuelPrice($fuel);

        $data = [
            'items' => [
                [
                    'id' => $fuel->getId(),
                    'volume' => -10,
                ],
            ],
        ];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'volume' => 'array:!empty',
        ], '$.errors.items[0]');
    }

    public function testSendPostVolumeIsGreaterThanAllowReturnError(ApiTester $I)
    {
        $I->authorizeAsAdmin();

        /** @var Type $fuel */
        $fuel = $I->haveFuelType(['fuelType' => 1]);
        $I->haveFuelPrice($fuel);

        $data = [
            'items' => [
                [
                    'id' => $fuel->getId(),
                    'volume' => 50001,
                ],
            ],
        ];
        $I->sendPOST($this->basePath, $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'volume' => 'array:!empty',
        ], '$.errors.items[0]');
    }

    /**
     * Allowed roles.
     *
     * @param ApiTester $I
     * @param Example   $example
     *
     * @throws \Exception
     *
     * @example { "role": "admin" }
     */
    public function testSendPostReturnSuccess(ApiTester $I, Example $example)
    {
        $myself = $I->haveCabinetUser(['role' => $example['role']]);
        $I->authorize($myself->getUserName());

        $client = $myself->getCompany()->getClient();

        /** @var Type $fuel */
        $fuel = $I->haveFuelType(['fuelType' => 1]);
        /** @var Price $price */
        $price = $I->haveFuelPrice($fuel);

        $volume = 50000;
        $data = [
            'items' => [
                [
                    'id' => $fuel->getId(),
                    'volume' => $volume,
                ],
            ],
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
            'id' => $id,
            'value_tax' => $this->shellInfo->getValueAddedTax(),
            'creation_date' => date('Y-m-d'),
            'export_status' => 1,
        ]);

        $I->seeNumRecords(1, 'invoices_items', [
            'invoice_id' => $id,
        ]);

        $I->seeInDatabase('invoices_items', [
            'invoice_id' => $id,
            'line_number' => 1,
            'fuel_code' => $fuel->getFuelCode(),
            'quantity' => $volume * 100,
            'price' => $price->getPriceWithTax(),
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
