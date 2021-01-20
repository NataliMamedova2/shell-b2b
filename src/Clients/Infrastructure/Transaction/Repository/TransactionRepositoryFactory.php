<?php

namespace App\Clients\Infrastructure\Transaction\Repository;

use App\Clients\Domain\Transaction\Card\Transaction;
use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Interfaces\Criteria\CriteriaFactory;

final class TransactionRepositoryFactory
{
    public function __invoke(
        EntityManagerInterface $entityManager,
        CriteriaFactory $criteriaFactory
    ): TransactionRepository {
        return new TransactionRepository($entityManager, $criteriaFactory, Transaction::class);
    }
}
