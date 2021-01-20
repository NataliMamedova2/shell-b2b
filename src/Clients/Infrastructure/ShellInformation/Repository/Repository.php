<?php

namespace App\Clients\Infrastructure\ShellInformation\Repository;

use App\Clients\Domain\ShellInformation\ShellInformation;

interface Repository extends \Infrastructure\Interfaces\Repository\Repository
{
    /**
     * @return ShellInformation
     */
    public function get(): ShellInformation;
}
