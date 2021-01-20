<?php

namespace App\Partners\Infrastructure\Repository\Transaction;

use App\Partners\Domain\Transaction\Transaction;
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
