<?php

declare(strict_types=1);

namespace Tests\Acceptance\Backend;

use Tests\AcceptanceTester;

final class ApiDocumentationCest
{
    private $basePath = '/admin/api/doc';

    public function testAuthorizedSeeDocumentation(AcceptanceTester $I): void
    {
        $I->loginAsRoot();

        $I->amOnPage($this->basePath);
        $I->waitForText('Download OpenAPI specification', 30);
    }

    public function testUnauthorizedSeeLoginPage(AcceptanceTester $I): void
    {
        $I->amOnPage($this->basePath);

        $I->seeCurrentUrlEquals('/admin/sign-in');
    }
}
