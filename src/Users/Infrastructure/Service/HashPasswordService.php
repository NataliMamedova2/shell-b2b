<?php

namespace App\Users\Infrastructure\Service;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

final class HashPasswordService implements \App\Users\Domain\User\Service\HashPasswordService
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function encode($user, string $plainPassword): string
    {
        return $this->passwordEncoder->encodePassword($user, $plainPassword);
    }
}
