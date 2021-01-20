<?php

namespace App\Application\Validator\Constraints;

use Psr\Container\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Infrastructure\Interfaces\Repository\Repository;

final class EntityExistValidator extends ConstraintValidator
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param object $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof EntityExist) {
            throw new UnexpectedTypeException($constraint, EntityExist::class);
        }

        if (false === $this->container->has($constraint->repository)) {
            throw new ConstraintDefinitionException(sprintf('Repository "%s" does not exist or private.', $constraint->repository));
        }

        /** @var Repository $repository */
        $repository = $this->container->get($constraint->repository);

        $criteria[$constraint->criteriaName] = $value;

        $result = $repository->find($criteria);

        if (empty($result)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $this->formatValue($value))
                ->setCode(EntityExist::NOT_EXIST_ERROR)
                ->addViolation();
        }
    }
}
