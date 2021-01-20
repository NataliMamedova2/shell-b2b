<?php

namespace App\Api\Action\Api\V1\Partners\Invoice\CreditDebtAction;

use App\Api\Crud\Interfaces\QueryHandler;
use App\Api\Crud\Interfaces\QueryRequest;
use App\Partners\Domain\Partner\Partner;
use App\Security\Partners\MyselfInterface;

final class CreditDebtService implements QueryHandler
{
    /**
     * @var MyselfInterface
     */
    private $myself;

    public function __construct(MyselfInterface $myself)
    {
        $this->myself = $myself;
    }

    public function handle(QueryRequest $queryRequest)
    {
        $partner = $this->myself->getPartner();

        if (!$partner instanceof Partner || $partner->getBalance() >= 0) {
            return [
                'amount' => 0,
            ];
        }

        $accountBalance = (int) round(abs($partner->getBalance()));

        return [
            'amount' => $accountBalance,
        ];
    }
}
