<?php

declare(strict_types=1);

namespace App\Security\Provider;

use App\Clients\Domain\User\Repository\UserRepository;
use App\Clients\Domain\User\User;
use App\Clients\Infrastructure\User\Criteria\Login;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

final class CabinetUserProvider implements UserProviderInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * BackendUserProvider constructor.
     *
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function loadUserByUsername($username): User
    {
        return $this->findUsername($username);
    }

    private function findUsername(string $username): User
    {
        $user = $this->userRepository->find([
            Login::class => $username,
        ]);

        if ($user instanceof User) {
            return $user;
        }

        throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
    }

    public function refreshUser(UserInterface $user): User
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }
        $username = $user->getEmail();

        return $this->findUsername($username);
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}
