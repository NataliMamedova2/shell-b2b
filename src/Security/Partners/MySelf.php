<?php

namespace App\Security\Partners;

use App\Partners\Domain\User\User;
use App\Partners\Domain\Partner\Partner;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

final class MySelf implements MyselfInterface
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
     * @var Partner|null
     */
    private $partner;

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

    public function getPartner(): Partner
    {
        if ($this->partner instanceof Partner) {
            return $this->partner;
        }
        $this->partner = $this->get()->getPartner();

        return $this->partner;
    }
}
