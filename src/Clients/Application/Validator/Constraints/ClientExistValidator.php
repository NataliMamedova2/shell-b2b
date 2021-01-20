<?php

namespace App\Clients\Application\Validator\Constraints;

use App\Clients\Domain\Client\Client;
use Symfony\Component\Validator\ConstraintValidator;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ClientExistValidator extends ConstraintValidator
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
     * @param Client|string|null $value
     * @param Constraint         $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof ClientExist) {
            throw new UnexpectedTypeException($constraint, ClientExist::class);
        }

        if (empty($value)) {
            return;
        }

        $clientId = $value;
        if ($value instanceof Client) {
            $clientId = $value->getId();
        }

        $entity = $this->repository->findById($clientId);

        if (empty($entity)) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
