<?php

namespace App\Clients\Infrastructure\Client\Criteria;

use App\Clients\Domain\Company\Company;
use App\Clients\Domain\RegisterToken\Register;
use App\Clients\Domain\User\User;
use App\Clients\Domain\User\ValueObject\Role as RoleValueObject;
use Doctrine\ORM\QueryBuilder;
use Infrastructure\Interfaces\Criteria\Criteria;

final class RegisterStatusCriteria
{
    public const REGISTERED = 'registered';
    public const NOT_REGISTERED = 'not-registered';
    public const RESEND_REGISTER_LINK = 'resend-register-link';

    public function __invoke(Criteria $criteria, $value)
    {
        /** @var QueryBuilder $query */
        $query = $criteria->getQueryBuilder();
        $alias = $query->getRootAliases()[0];

        if (self::REGISTERED === $value) {
            $companyAlias = 'company';
            $query->innerJoin(Company::class, $companyAlias, 'WITH', "$companyAlias.client = $alias.id");

            $userAlias = 'company_user';
            $query->innerJoin(User::class, $userAlias, 'WITH', "$companyAlias.id = $userAlias.company");
            $query->andWhere(
                "CONTAINS({$userAlias}.roles, :role) = true"
            );

            $query->setParameter('role', json_encode([RoleValueObject::admin()->getValue()]));
        }

        if (self::NOT_REGISTERED === $value) {
            $expr = $query->getEntityManager()->getExpressionBuilder();
            $em = $query->getEntityManager();
            $companyDql = $em->createQueryBuilder()
                ->select('identity(c.client)')
                ->from(Company::class, 'c')
                ->getDQL();

            $registerLinkDql = $em->createQueryBuilder()
                ->select('identity(r.client)')
                ->from(Register::class, 'r')
                ->getDQL();

            $companyDql2 = $em->createQueryBuilder()
                ->select('identity(c2.client)')
                ->from(Company::class, 'c2')
                ->innerJoin(User::class, 'company_user', 'WITH', 'c2.id = company_user.company')
                ->where(
                    'CONTAINS(company_user.roles, :role) = true'
                )->getDQL();

            $query->andWhere($expr->orX(
                $expr->notIn("$alias.id", $companyDql),
                $expr->notIn("$alias.id", $companyDql2)
            ))->andWhere($expr->notIn("$alias.id", $registerLinkDql))->setParameter('role', json_encode([RoleValueObject::admin()->getValue()]));
        }

        if (self::RESEND_REGISTER_LINK === $value) {
            $joinAlias = 'company_register_request';
            $query->innerJoin(Register::class, $joinAlias, 'WITH', "$joinAlias.client = $alias.id");
        }
    }
}
