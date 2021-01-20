<?php

declare(strict_types=1);

namespace Tests\Acceptance\Backend;

use Tests\AcceptanceTester;
use Tests\Helper\Fixtures;

final class SingInCest
{
    /**
     * @var Fixtures
     */
    protected $fixtures;

    protected function _inject(Fixtures $fixtures)
    {
        $this->fixtures = $fixtures;
    }

    public function _before(AcceptanceTester $I): void
    {
        $I->amOnPage('/admin/sign-in');
    }

    public function testLoginAndRememberAsAdmin(AcceptanceTester $I): void
    {
        $username = 'admin';
        $password = '111';
        $this->fixtures->createUser([
            'username' => $username,
        ]);

        $I->fillField('username', $username);
        $I->fillField('password', $password);
        $I->checkOption('_remember_me');

        $I->click('Log In');

        $I->seeCurrentUrlEquals('/admin');
        $I->waitForText($username, null, '//nav//div[@class="navbar-header"]');
    }

    public function testCheckIncorrectLogin(AcceptanceTester $I): void
    {
        $username = 'davert';
        $password = '111';

        $I->fillField('username', $username);
        $I->fillField('password', $password);

        $I->click('//form[@id="loginform"]//button[@type="submit"]');

        $I->seeInCurrentUrl('/admin/sign-in');
        $I->waitForElement('//div[contains(@class, "alert-danger")]');
    }

    public function testCheckIncorrectPasswordAsRoot(AcceptanceTester $I): void
    {
        $username = 'root';
        $password = 'qwerty';

        $I->fillField('username', $username);
        $I->fillField('password', $password);

        $I->click('Log In');

        $I->seeInCurrentUrl('/admin/sign-in');
        $I->waitForElement('//div[contains(@class, "alert-danger")]');
    }

    public function testCheckSQLInjection(AcceptanceTester $I): void
    {
        $I->fillField('username', 'root');
        $I->fillField('password', '\' OR 100=100 --');

        $I->click('Log In');

        $I->seeInCurrentUrl('/admin/sign-in');
        $I->waitForElement('//div[contains(@class, "alert-danger")]');
    }

    public function testCheckSQLInjectionDropTable(AcceptanceTester $I): void
    {
        $I->fillField('username', 'root');
        $I->fillField('password', '\'; DROP TABLE users --');

        $I->click('Log In');

        $I->seeInCurrentUrl('/admin/sign-in');
        $I->waitForElement('//div[contains(@class, "alert-danger")]');
    }

//    public function testLogout(AcceptanceTester $I): void
//    {
//        $this->testLoginAndRememberAsAdmin($I);
//
//        $I->waitForElementClickable('//a[@class="dropdown-toggle profile-pic"]');
//
//        $I->click('//a[@class="dropdown-toggle profile-pic"]');
//        $I->click('//a[@href="/admin/logout"]');
//
//        $I->seeInCurrentUrl('/');
//
//        $I->amOnPage('/admin');
//        $I->seeInCurrentUrl('/admin/sign-in');
//    }

    public function testLoginAsAdmin(AcceptanceTester $I): void
    {
        $user = $this->fixtures->createUser();

        $username = $user['email'];
        $password = '111';

        $I->fillField('username', $username);
        $I->fillField('password', $password);
        $I->checkOption('_remember_me');

        $I->click('Log In');

        $I->seeCurrentUrlEquals('/admin');
        $I->waitForText($user['username'], null, '//nav//div[@class="navbar-header"]');
    }
}
