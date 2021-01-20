<?php

namespace App\Users\Application\Validator\Constraints;

use App\Users\Domain\User\UseCase\Create;
use App\Users\Domain\User\UseCase\Update;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class ManagerIdExistValidator extends ConstraintValidator
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
     * @param Create\HandlerRequest|Update\HandlerRequest $object
     */
    public function validate($object, Constraint $constraint): void
    {
        if (!$constraint instanceof ManagerIdExist) {
            throw new UnexpectedTypeException($constraint, ManagerIdExist::class);
        }

        if (!empty($object->manager1CId) && in_array($object->manager1CId, ['root'])) {
            $this->context->buildViolation($constraint->managerId1CMessage)
                ->setParameter('{{ manager1CId }}', $object->manager1CId)
                ->atPath('manager1CId')
                ->addViolation();

            return;
        }

        if (!empty($object->manager1CId)) {
            $criteria = [
                'manager1CId_equalTo' => $object->manager1CId,
            ];
            if ($object instanceof Update\HandlerRequest) {
                $criteria['id_notEqualTo'] = $object->getId();
            }

            $entity = $this->repository->find($criteria);
            if (!empty($entity)) {
                $this->context->buildViolation($constraint->managerId1CMessage)
                    ->setParameter('{{ manager1CId }}', $object->manager1CId)
                    ->atPath('manager1CId')
                    ->addViolation();
            }
        }
    }
}
