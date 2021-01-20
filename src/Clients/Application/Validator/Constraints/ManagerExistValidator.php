<?php

namespace App\Clients\Application\Validator\Constraints;

use Infrastructure\Interfaces\Repository\Repository;
use Psr\Container\ContainerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ManagerExistValidator extends ConstraintValidator
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
     * @param object     $object
     *
     * @param Constraint $constraint
     */
    public function validate($object, Constraint $constraint): void
    {
        if (!$constraint instanceof ManagerExist) {
            throw new UnexpectedTypeException($constraint, ManagerExist::class);
        }

        if (false === property_exists($object, $constraint->property)) {
            throw new ConstraintDefinitionException(sprintf('Property "%s" does not exist.', $constraint->property));
        }

        if (false === $this->container->has($constraint->repository)) {
            throw new ConstraintDefinitionException(sprintf('Repository "%s" does not exist.', $constraint->repository));
        }

        /** @var Repository $repository */
        $repository = $this->container->get($constraint->repository);

        $criteria = [$constraint->criteria => $object->{$constraint->property}];
        $result = $repository->find($criteria);

        if (empty($result)) {
            $this->context->buildViolation($constraint->message)
                ->atPath($constraint->errorPath)
                ->setCode(ManagerExist::NOT_EXIST_ERROR)
                ->addViolation();
        }
    }
}
