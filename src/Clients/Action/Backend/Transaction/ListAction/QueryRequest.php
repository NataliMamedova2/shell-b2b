<?php

namespace App\Clients\Action\Backend\Transaction\ListAction;

use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Domain\Transaction\Card\ValueObject\Type;
use App\Clients\Infrastructure\Client\Criteria\ClientIdLike;
use App\Clients\Infrastructure\Transaction\Criteria\AzsNameLikeCriteria;
use App\Clients\Infrastructure\Transaction\Criteria\ClientFullNameLikeCriteria;
use App\Clients\Infrastructure\Transaction\Criteria\SupplyTypeCriteria;
use App\Clients\Infrastructure\User\Criteria\ClientManagerCriteria;
use App\Users\Domain\User\User;
use CrudBundle\Action\ListAction\QueryRequest as BaseQueryRequest;
use CrudBundle\Interfaces\ListQueryRequest;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class QueryRequest extends BaseQueryRequest implements ListQueryRequest
{
    /**
     * @var User
     */
    public $user;

    /**
     * @var AuthorizationCheckerInterface
     */
    public $authorizationChecker;

    public function __construct(
        RequestStack $requestStack,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $request = $requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \InvalidArgumentException('Request not found');
        }

        $token = $tokenStorage->getToken();
        if (null === $token) {
            throw new \InvalidArgumentException('Token not found');
        }

        /** @var User $user */
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            throw new \InvalidArgumentException('User not found');
        }

        $this->user = $user;
        $this->authorizationChecker = $authorizationChecker;

        parent::__construct($request);
    }

    public function getCriteria(): array
    {
        $request = $this->getRequest();
        $criteria = [];

        if ($clientId = $request->get('clientId')) {
            $criteria[ClientIdLike::class] = $clientId;
        }
        if ($clientName = $this->getRequest()->get('clientName')) {
            $criteria[ClientFullNameLikeCriteria::class] = $clientName;
        }
        if (null != (string) $request->get('cardNumber')) {
            $criteria['cardNumber_like'] = '%'.$request->get('cardNumber').'%';
        }
        if (null != (string) $request->get('azsName')) {
            $criteria[AzsNameLikeCriteria::class] = $request->get('azsName');
        }
        if (null != (string) $request->get('dateFrom')) {
            $date = \DateTime::createFromFormat('d-m-Y', $request->get('dateFrom'));
            $dateErrors = \DateTime::getLastErrors();
            if (0 === $dateErrors['error_count']) {
                $date->setTime(0, 0, 0);
                $criteria['postDate_greaterThanOrEqualTo'] = $date;
            }
        }
        if (null != (string) $request->get('dateTo')) {
            $date = \DateTime::createFromFormat('d-m-Y', $request->get('dateTo'));
            $dateErrors = \DateTime::getLastErrors();
            if (0 === $dateErrors['error_count']) {
                $date->setTime(23, 59, 59);
                $criteria['postDate_lessThanOrEqualTo'] = $date;
            }
        }
        $type = $request->get('type');
        $typeNames = Type::getNames();
        if (in_array($type, $typeNames)) {
            $criteria['type_equalTo'] = Type::fromName($type)->getValue();
        }

        $supplyTypes = (array) $request->get('supplyTypes');
        if (!empty($supplyTypes)) {
            $typeNames = FuelType::getNames();
            $typesValues = [];
            foreach ($supplyTypes as $selectedType) {
                if (in_array($selectedType, $typeNames)) {
                    $typesValues[] = FuelType::fromName($selectedType)->getValue();
                }
            }
            if (!empty($typesValues)) {
                $criteria[SupplyTypeCriteria::class] = $typesValues;
            }
        }

        $supplies = (array) $request->get('supplies');
        if (!empty($supplies)) {
            $fuelCodes = array_diff($supplies, ['']);
            if (!empty($fuelCodes)) {
                $criteria['fuelCode_in'] = $fuelCodes;
            }
        }

        if ($this->user instanceof User && true === $this->authorizationChecker->isGranted('ROLE_MANAGER')) {
            $criteria[ClientManagerCriteria::class] = (string) $this->user->getManager1CId();
        }

        if ($manager = $request->get('manager')) {
            $criteria[ClientManagerCriteria::class] = $manager;
        }

        if (null != $request->get('dateFrom') && null === $request->get('dateTo')) {
            $date = new \DateTime('today');
            $date->setTime(0, 0, 0);
            $criteria['postDate_greaterThanOrEqualTo'] = $date;
        }

        return $criteria;
    }

    public function getOrder(): array
    {
        return ['postDate' => 'DESC'];
    }
}
