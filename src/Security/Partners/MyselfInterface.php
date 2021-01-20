<?php

namespace App\Security\Partners;

use App\Partners\Domain\Partner\Partner;
use App\Partners\Domain\User\User;

interface MyselfInterface
{
    public function get(): User;

    public function getPartner(): Partner;
}
