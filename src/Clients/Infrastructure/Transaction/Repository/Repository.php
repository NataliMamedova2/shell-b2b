<?php

namespace App\Clients\Infrastructure\Transaction\Repository;

use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Transaction\Card\ValueObject\Type;

interface Repository extends \Infrastructure\Interfaces\Repository\Repository
{
    public function calculateDebitSum(
        string $clientId,
        string $cardNumber,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): int;

    public function calculateClientDebitSum(
        Client $client,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): int;

    public function calculateFuelQuantitySum(
        string $clientId,
        string $cardNumber,
        string $fuelCode,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): int;

    public function calculateClientDebitSumByMonths(
        Client $client,
        Type $type,
        \DateTimeInterface $startDate,
        \DateTimeInterface $endDate
    ): array;

    public function getClientFuelCodes(Client $client): array;
    public function getFuelCodes(): array;
}
