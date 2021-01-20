<?php

declare(strict_types=1);

namespace Tests;

use Codeception\Actor;
use Codeception\Lib\Friend;

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
class AcceptanceTester extends Actor
{
    use _generated\AcceptanceTesterActions {
        amOnPage as traitAmOnPage;
        fillField as traitFillField;
    }

    public function loginAsRoot(): void
    {
        $username = 'root';
        $password = '111';

        $this->login($username, $password);
    }

    /**
     * @param $username
     * @param int $password
     */
    public function login($username, $password = 111): void
    {
        $this->amOnPage('/');
        if ($this->loadSessionSnapshot('login')) {
            return;
        }

        $this->amOnPage('/admin/sign-in');
        $this->submitForm('#loginform', [
            'username' => $username,
            'password' => $password,
        ]);

        $this->saveSessionSnapshot('login');

        $this->seeInCurrentUrl('/admin');
        $this->wait(5);
    }

    public function dontSeeErrors()
    {
        $this->dontSee('Notice');
        $this->dontSee('Warning');
        $this->dontSee('Error');
        $this->dontSee('Exception');
    }

    public function fillTinyMceEditorById($id, $content)
    {
        $this->fillTinyMceEditor('id', $id, $content);
    }

    private function fillTinyMceEditor($attribute, $value, $content)
    {
        $this->fillRteEditor(
            \Facebook\WebDriver\WebDriverBy::xpath(
                '//textarea[@'.$attribute.'="'.$value.'"]/../div[contains(@class, \'tox-tinymce\')]//iframe'
            ),
            $content
        );
    }

    private function fillRteEditor($selector, $content)
    {
        $this->executeInSelenium(
            function (\Facebook\WebDriver\Remote\RemoteWebDriver $webDriver) use ($selector, $content) {
                $webDriver->switchTo()->frame(
                    $webDriver->findElement($selector)
                );

                $webDriver->executeScript(
                    'arguments[0].innerHTML = "'.addslashes($content).'"',
                    [$webDriver->findElement(\Facebook\WebDriver\WebDriverBy::tagName('body'))]
                );

                $webDriver->switchTo()->defaultContent();
            }
        );
    }

    public function fillTinyMceEditorByName($name, $content)
    {
        $this->fillTinyMceEditor('name', $name, $content);
    }
}
