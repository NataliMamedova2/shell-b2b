<?php

namespace Tests;

use App\Clients\Domain\User\User;
use Codeception\Actor;
use Codeception\Lib\Friend;
use Codeception\Util\JsonType;

/**
 * Inherited Methods.
 *
 * @method void   wantToTest($text)
 * @method void   wantTo($text)
 * @method void   execute($callable)
 * @method void   expectTo($prediction)
 * @method void   expect($prediction)
 * @method void   amGoingTo($argumentation)
 * @method void   am($role)
 * @method void   lookForwardTo($achieveValue)
 * @method void   comment($description)
 * @method Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class ApiTester extends Actor
{
    use _generated\ApiTesterActions {
        sendGET as traitSendGET;
        sendPOST as traitSendPOST;
        sendOPTIONS as traitSendOPTIONS;
        seeResponseMatchesJsonType as traitSeeResponseMatchesJsonType;
    }

    protected $accessToken;

    public function sendGET($url, $params = [])
    {
        if ($this->accessToken) {
            $this->amBearerAuthenticated($this->accessToken);
        }

        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->haveHttpHeader('X-Requested-With', 'Codeception');

        return $this->traitSendGET($url, $params);
    }

    public function sendPOST($url, $params = [])
    {
        if ($this->accessToken) {
            $this->amBearerAuthenticated($this->accessToken);
        }

        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->haveHttpHeader('X-Requested-With', 'Codeception');

        return $this->traitSendPOST($url, $params);
    }

    public function sendOPTIONS($url, $params = [])
    {
        if ($this->accessToken) {
            $this->amBearerAuthenticated($this->accessToken);
        }

        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->haveHttpHeader('X-Requested-With', 'Codeception');

        return $this->traitSendOPTIONS($url, $params);
    }

    /**
     * @param        $login
     * @param string $password default 111
     *
     * @throws \Exception
     */
    public function authorize($login, $password = '111')
    {
        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->haveHttpHeader('X-Requested-With', 'Codeception');

        $this->traitSendPOST('/api/v1/oauth', [
            'username' => $login,
            'password' => $password,
        ]);

        $this->seeResponseIsJson();

        $accessToken = $this->grabDataFromResponseByJsonPath('$.token');

        $this->accessToken = $accessToken[0];
    }

    /**
     * @return User
     *
     * @throws \Exception
     */
    public function authorizeAsAdmin()
    {
        $user = $this->haveCabinetUser(['role' => 'admin']);

        $this->authorize($user->getUserName());

        return $user;
    }

    /**
     * @return User
     *
     * @throws \Exception
     */
    public function authorizeAsManager()
    {
        $user = $this->haveCabinetUser(['role' => 'manager']);

        $this->authorize($user->getUserName());

        return $user;
    }

    /**
     * @return User
     *
     * @throws \Exception
     */
    public function authorizeAsAccountant()
    {
        $user = $this->haveCabinetUser(['role' => 'accountant']);

        $this->authorize($user->getUserName());

        return $user;
    }

    /**
     * @param array $data
     *
     * @return mixed|null
     */
    public function seeResponseEqualsJson(array $data = [])
    {
        return $this->seeResponseEquals(json_encode($data));
    }

    public function seeResponseMatchesJsonType($jsonType, $jsonPath = null)
    {
        JsonType::addCustomFilter('/enum\((.*?)\)/', function ($value, string $param) {
            if (isset($param) && is_string($param)) {
                $param = json_decode($param);
            }

            return in_array($value, $param);
        });

        return $this->traitSeeResponseMatchesJsonType($jsonType, $jsonPath);
    }

    public function seeResponseCodeIs(int $NOT_FOUND)
    {
    }

    public function seeResponseIsJson()
    {
    }

    public function seeResponseContainsJson(array $array)
    {
    }
}
