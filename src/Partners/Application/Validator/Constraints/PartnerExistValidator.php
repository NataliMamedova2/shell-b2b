<?php

namespace App\Partners\Application\Validator\Constraints;

use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use App\Partners\Domain\Partner\Partner;

final class PartnerExistValidator extends ConstraintValidator
{
    /**
     * @var Repository
     */
    private $repository;

    public function __construct(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Partner|string|null $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof PartnerExist) {
            throw new UnexpectedTypeException($constraint, PartnerExist::class);
        }

        if (empty($value)) {
            return;
        }

        $clientId = $value;
        if ($value instanceof Partner) {
            $clientId = $value->getId();
        }

        $entity = $this->repository->findById($clientId);

        if (empty($entity)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
