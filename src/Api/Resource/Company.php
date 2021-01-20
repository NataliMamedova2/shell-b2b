<?php

namespace App\Api\Resource;

final class Company implements Model
{
    public $name;
    public $legalName;
    public $accountingEmail;
    public $accountingPhone;
    public $directorEmail;
    public $postalAddress;

    /**
     * @param \App\Clients\Domain\Company\Company $company
     *
     * @return Model
     */
    public function prepare($company): Model
    {
        $client = $company->getClient();
        $accounting = $company->getAccounting();

        $this->name = $company->getName();
        $this->legalName = $client->getFullName();
        $this->accountingEmail = $accounting->getEmail();
        $this->accountingPhone = $accounting->getPhone();
        $this->directorEmail = $company->getEmail();
        $this->postalAddress = $company->getPostalAddress();

        return $this;
    }
}
