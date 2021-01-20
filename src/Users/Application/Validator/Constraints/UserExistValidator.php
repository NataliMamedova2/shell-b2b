<?php

namespace App\Users\Application\Validator\Constraints;

use Symfony\Component\Validator\ConstraintValidator;
use Infrastructure\Interfaces\Repository\Repository;
use App\Users\Domain\User\UseCase\Create;
use App\Users\Domain\User\UseCase\Update;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UserExistValidator extends ConstraintValidator
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
     * @param Constraint                                  $constraint
     */
    public function validate($object, Constraint $constraint): void
    {
        if (!$constraint instanceof UserExist) {
            throw new UnexpectedTypeException($constraint, UserExist::class);
        }

        if (!empty($object->username) && in_array($object->username, ['root'])) {
            $this->context->buildViolation($constraint->usernameMessage)
                ->setParameter('{{ username }}', $object->username)
                ->atPath('username')
                ->addViolation();

            return;
        }

        if (!empty($object->email)) {
            $criteria = [
                'email_equalTo' => $object->email,
            ];
            if ($object instanceof Update\HandlerRequest) {
                $criteria['id_notEqualTo'] = $object->getId();
            }

            $entity = $this->repository->find($criteria);
            if (!empty($entity)) {
                $this->context->buildViolation($constraint->emailMessage)
                    ->setParameter('{{ email }}', $object->email)
                    ->atPath('email')
                    ->addViolation();
            }
        }

        if (!empty($object->username)) {
            $criteria = [
                'username_equalTo' => $object->username,
            ];
            if ($object instanceof Update\HandlerRequest) {
                $criteria['id_notEqualTo'] = $object->getId();
            }
            $entity = $this->repository->find($criteria);

            if (!empty($entity)) {
                $this->context->buildViolation($constraint->usernameMessage)
                    ->setParameter('{{ username }}', $object->username)
                    ->atPath('username')
                    ->addViolation();
            }
        }
    }
}
