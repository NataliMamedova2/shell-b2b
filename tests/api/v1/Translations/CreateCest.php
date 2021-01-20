<?php

namespace Tests\Api\V1\Translations;

use App\Users\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class CreateCest
{
    private $basePath = '/api/v1/translations/create/{locale}';

    /**
     * @var User
     */
    private $user;

    public function _before(ApiTester $I)
    {
        $this->user = $I->haveCabinetUser(['username' => 'admin']);
    }

    public function localeDataProvider(): array
    {
        return [
            [
                'locale' => 'uk',
            ],
            [
                'locale' => 'en',
            ],
        ];
    }

    /**
     * @dataProvider localeDataProvider
     *
     * @throws \Exception
     */
    public function testSendPOSTReturn200(ApiTester $I, Example $example)
    {
        $I->authorize('admin');

        $url = $this->getUrl($example->getIterator()->getArrayCopy());

        $data = [
            'key' => 'trans_key',
            'translation' => 'trans text',
        ];

        $I->sendPOST($url, $data);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    private function getUrl(array $params): string
    {
        $data = [];
        foreach ($params as $k => $value) {
            $data['{'.$k.'}'] = $value;
        }

        return strtr($this->basePath, $data);
    }

    /*
     * @dataProvider localeDataProvider
     *
     * @throws \Exception
     */
   /* public function testSendPOSTEmptyDataReturn400(ApiTester $I, Example $example)
    {
        $I->authorize('admin');

        $url = $this->getUrl($example->getIterator()->getArrayCopy());

        $data = [
            'key' => '',
            'translation' => '',
        ];

        $I->sendPOST($url, $data);

        $I->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $I->seeResponseIsJson();

        $I->seeResponseMatchesJsonType([
            'key' => 'array',
            'translation' => 'array',
        ]);
    }*/

    /* public function testSendPOSTNoLocaleReturn404(ApiTester $I)
     {
         $I->authorize('admin');

         $url = '/api/v1/translations/create';

         $data = [
             'key' => 'trans_key',
             'translation' => 'trans text',
         ];

         $I->sendPOST($url, $data);

         $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
         $I->seeResponseIsJson();
     }

     public function testUnauthorizedReturn200(ApiTester $I)
     {
         $data = [
             'key' => 'trans_key',
             'translation' => 'trans text',
         ];

         $I->sendPOST($this->getUrl(['locale' => 'uk']), $data);

         $I->seeResponseCodeIs(HttpCode::OK);
         $I->seeResponseIsJson();
     }

     public function testSendGETReturn405(ApiTester $I)
     {
         $I->sendGET($this->getUrl(['locale' => 'uk']));

         $I->seeResponseCodeIs(HttpCode::METHOD_NOT_ALLOWED);
         $I->seeResponseIsJson();

         $I->seeResponseContainsJson([
             'code' => 405,
         ]);
     }*/
}
