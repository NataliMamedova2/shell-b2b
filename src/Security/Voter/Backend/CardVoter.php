<?php

namespace App\Security\Voter\Backend;

use App\Clients\Domain\Card\Card;
use App\Clients\Infrastructure\FuelCard\Criteria\ClientManagerCriteria;
use App\Users\Domain\User\User;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class CardVoter extends Voter
{
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var Repository
     */
    private $cardRepository;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, Repository $cardRepository)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->cardRepository = $cardRepository;
    }

    protected function supports($attribute, $subject): bool
    {
        if (!empty($subject) && Card::class === get_class($subject)) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN') || true === $this->authorizationChecker->isGranted('ROLE_MANAGER_CALL_CENTER')) {
            return true;
        }

        if (true === $this->authorizationChecker->isGranted('ROLE_MANAGER')) {
            $user = $token->getUser();
            if (!$user instanceof User) {
                return false;
            }

            $criteria = [
                'cardNumber_equalTo' => $subject->getCardNumber(),
                ClientManagerCriteria::class => $user->getManager1CId(),
            ];

            $card = $this->cardRepository->find($criteria);

            if ($card === $subject) {
                return true;
            }
        }

        return false;
    }
}
