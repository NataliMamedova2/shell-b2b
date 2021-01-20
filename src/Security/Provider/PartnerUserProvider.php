<?php

namespace App\Security\Provider;

use App\Partners\Domain\User\Repository\UserRepository;
use App\Partners\Domain\User\User;
use App\Users\Infrastructure\Criteria\Login;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class PartnerUserProvider implements UserProviderInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * BackendUserProvider constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $username
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

        if ($user instanceof User && true === \in_array('ROLE_PARTNER', $user->getRoles())) {
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
     */
    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}
