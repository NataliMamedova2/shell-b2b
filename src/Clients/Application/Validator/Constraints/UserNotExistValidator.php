<?php

namespace App\Clients\Application\Validator\Constraints;

use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Domain\User\UseCase\Create;
use App\Clients\Domain\User\UseCase\Update;
use App\Clients\Domain\User\User;
use App\Clients\Infrastructure\User\Criteria\Login;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class UserNotExistValidator extends ConstraintValidator
{
    /**
     * @var UserRepository
     */
    private $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param Create\HandlerRequest|Update\HandlerRequest $object
     * @param Constraint $constraint
     */
    public function validate($object, Constraint $constraint): void
    {
        if (!$constraint instanceof UserNotExist) {
            throw new UnexpectedTypeException($constraint, UserNotExist::class);
        }

        if (!empty($object->username)) {
            $user = $this->repository->find([
                Login::class => $object->username,
            ]);

            if (!$user instanceof User) {
                $this->context->buildViolation($constraint->messageNotExist)
                    ->atPath('username')
                    ->addViolation();
            }
        }
    }
}
