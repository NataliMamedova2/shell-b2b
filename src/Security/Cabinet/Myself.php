<?php

namespace App\Security\Cabinet;

use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\User\User;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class Myself implements MyselfInterface
{
    /**
     * @var TokenInterface|null
     */
    private $token;

    /**
     * @var User|null
     */
    private $myself;

    /**
     * @var Company|null
     */
    private $company;

    /**
     * @var Client|null
     */
    private $client;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->token = $tokenStorage->getToken();
        if (!$this->token instanceof TokenInterface) {
            $this->throwUnauthorizedException();
        }
    }

    private function throwUnauthorizedException()
    {
        throw new UnauthorizedHttpException('Bearer');
    }

    public function get(): User
    {
        if ($this->myself instanceof User) {
            return $this->myself;
        }
        /** @var User $user */
        $user = $this->token->getUser();
        if (!$user instanceof User) {
            $this->throwUnauthorizedException();
        }
        $this->myself = $user;

        return $this->myself;
    }

    public function getCompany(): Company
    {
        if ($this->company instanceof Company) {
            return $this->company;
        }
        $this->company = $this->get()->getCompany();

        return $this->company;
    }

    public function getClient(): Client
    {
        if ($this->client instanceof Client) {
            return $this->client;
        }
        $this->client = $this->getCompany()->getClient();

        return $this->client;
    }
}
