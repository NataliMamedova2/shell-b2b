<?php
namespace App\Security\Application\Partners;

use App\Users\Domain\User\User;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class PartnersUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $identity): void
    {
        if (!$identity instanceof User) {
            return;
        }

        if (false === $identity->isActive()) {
            $exception = new DisabledException();
            $exception->setUser($identity);
            throw $exception;
        }
    }

    public function checkPostAuth(UserInterface $identity): void
    {
        if (!$identity instanceof User) {
            return;
        }
    }
}