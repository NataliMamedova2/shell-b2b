<?php

namespace App\Clients\Infrastructure\User\Service;

use App\Clients\Domain\User\Service\PasswordEncoder as DomainPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\PasswordEncoderInterface;
use App\Clients\Domain\User\User;

final class PasswordEncoder implements DomainPasswordEncoder
{
    /**
     * @var PasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(EncoderFactoryInterface $encoderFactory)
    {
        $encoder = $encoderFactory->getEncoder(User::class);

        $this->passwordEncoder = $encoder;
    }

    public function encode(string $password): string
    {
        return $this->passwordEncoder->encodePassword($password, null);
    }
}
