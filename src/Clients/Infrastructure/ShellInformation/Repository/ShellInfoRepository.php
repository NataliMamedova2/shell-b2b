<?php

namespace App\Clients\Infrastructure\ShellInformation\Repository;

use App\Clients\Domain\ShellInformation\ShellInformation;
use Infrastructure\Exception\InvalidArgumentException;
use Infrastructure\Repository\DoctrineRepository;

final class ShellInfoRepository extends DoctrineRepository implements Repository
{
    /**
     * @return ShellInformation
     */
    public function get(): ShellInformation
    {
        /** @var ShellInformation $shellInfo */
        $shellInfo = $this->find([]);

        if (!$shellInfo instanceof ShellInformation) {
            throw new InvalidArgumentException('ShellInformation not found');
        }

        return $shellInfo;
    }
}
