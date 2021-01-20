<?php

namespace Tests\Api\V1\Translations;

use App\Users\Domain\User\User;
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\ApiTester;

final class ListCest
{
    private $basePath = '/api/v1/translations/{locale}';

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

    private function getUrl(array $params): string
    {
        $data = [];
        foreach ($params as $k => $value) {
            $data['{' . $k . '}'] = $value;
        }

        return strtr($this->basePath, $data);
    }

    /**
     * @dataProvider localeDataProvider
     *
     * @param ApiTester $I
     * @param Example $example
     *
     * @throws \Exception
     */
    public function testSendGetReturn200(ApiTester $I, Example $example)
    {
        $I->authorize('admin');

        $url = $this->getUrl($example->getIterator()->getArrayCopy());
        $I->sendGET($url);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
    }

    public function testSendGetNoLocaleReturn404(ApiTester $I)
    {
        $I->authorize('admin');

        $url = '/api/v1/translations';
        $I->sendGET($url);

        $I->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $I->seeResponseIsJson();
    }

    /*  public function testUnauthorizedReturn201(ApiTester $I)
      {
          $I->sendGET($this->getUrl(['locale' => 'uk']));

          $I->seeResponseCodeIs(HttpCode::OK);
          $I->seeResponseIsJson();
      }

      public function testSendPOSTReturn405(ApiTester $I)
      {
          $I->sendPOST($this->getUrl(['locale' => 'uk']));

          $I->seeResponseCodeIs(HttpCode::METHOD_NOT_ALLOWED);
          $I->seeResponseIsJson();

          $I->seeResponseContainsJson([
              'code' => 405,
          ]);
      }*/
}
