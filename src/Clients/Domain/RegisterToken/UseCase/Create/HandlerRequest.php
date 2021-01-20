<?php

namespace App\Clients\Domain\RegisterToken\UseCase\Create;

use App\Application\Validator\Constraints as AppAssert;
use App\Clients\Application\Validator\Constraints\ClientExist as ClientEntityExist;
use App\Clients\Application\Validator\Constraints\ManagerExist as ManagerExist;
use App\Clients\Domain\Client\Client;
use Domain\Interfaces\HandlerRequest as DomainHandlerRequest;
use Symfony\Component\Validator\Constraints as Assert;

/**
 *
 * @ManagerExist(
 *     property="client",
 *     errorPath="email",
 *     repository="app.users.infrastructure.user.repository",
 *     criteria="\App\Users\Infrastructure\Criteria\ManagerForClient",
 *     message="Register Manager for this client"
 * )
 */
final class HandlerRequest implements DomainHandlerRequest
{
    /**
     * @var Client
     *
     * @Assert\NotBlank()
     * @ClientEntityExist()
     */
    public $client;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min=5, max=64)
     * @AppAssert\Email(mode="strict")
     */
    public $email;
}
