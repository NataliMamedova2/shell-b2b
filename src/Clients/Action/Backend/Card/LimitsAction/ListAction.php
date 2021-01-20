<?php

namespace App\Clients\Action\Backend\Card\LimitsAction;

use App\Application\Domain\ValueObject\ExportStatus;
use App\Application\Infrastructure\Criteria\ExportStatusCriteria;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Domain\FuelLimit\FuelLimit;
use App\Clients\Domain\FuelLimit\ValueObject\PurseActivity;
use App\Clients\Infrastructure\Fuel\Criteria\FuelLimitByType;
use App\Clients\Infrastructure\Fuel\Criteria\IndexByFuelCode;
use App\Clients\Infrastructure\Transaction\Repository\Repository as TransactionRepository;
use CrudBundle\Action\Response;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ListAction
{
    /**
     * @var Repository
     */
    private $cardRepository;
    /**
     * @var Repository
     */
    private $cardLimitsRepository;
    /**
     * @var Repository
     */
    private $fuelTypeRepository;
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(
        Repository $cardRepository,
        Repository $cardLimitsRepository,
        Repository $fuelTypeRepository,
        TransactionRepository $transactionRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->cardRepository = $cardRepository;
        $this->cardLimitsRepository = $cardLimitsRepository;
        $this->fuelTypeRepository = $fuelTypeRepository;
        $this->transactionRepository = $transactionRepository;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function __invoke(string $id, Request $request): Response
    {
        $card = $this->cardRepository->findById($id);

        if (!$card instanceof Card) {
            throw new NotFoundHttpException();
        }

        if (false === $this->authorizationChecker->isGranted('view', $card)) {
            throw new AccessDeniedException('Access Denied');
        }

        $defaultFuelType = 'fuel';
        $type = $request->get('type', $defaultFuelType);
        if (false === in_array($type, FuelType::getNames())) {
            $type = $defaultFuelType;
        }

        $limits = $this->getCardLimits($card, FuelType::fromName($type));

        $cardNumber = $card->getCardNumber();
        $countFuelLimits = $this->cardLimitsRepository->count([
            'cardNumber_equalTo' => $cardNumber,
            'purseActivity_equalTo' => PurseActivity::active()->getValue(),
            FuelLimitByType::class => FuelType::fuel()->getValue(),
        ]);

        $countGoodsLimits = $this->cardLimitsRepository->count([
            'cardNumber_equalTo' => $cardNumber,
            'purseActivity_equalTo' => PurseActivity::active()->getValue(),
            FuelLimitByType::class => FuelType::goods()->getValue(),
        ]);

        $countServicesLimits = $this->cardLimitsRepository->count([
            'cardNumber_equalTo' => $cardNumber,
            'purseActivity_equalTo' => PurseActivity::active()->getValue(),
            FuelLimitByType::class => FuelType::service()->getValue(),
        ]);

        $countLimitsOnModeration = $this->cardLimitsRepository->count([
            'cardNumber_equalTo' => $cardNumber,
            ExportStatusCriteria::class => ExportStatus::cantBeEditedStatuses(),
        ]);

        return new Response([
            'result' => [
                'card' => $card,
                'moneyLimits' => $limits['moneyLimits'],
                'limits' => $limits['limits'],
                'haveFuelLimits' => $countFuelLimits > 0 ? true : false,
                'haveGoodsLimits' => $countGoodsLimits > 0 ? true : false,
                'haveServicesLimits' => $countServicesLimits > 0 ? true : false,
                'haveLimitsOnModeration' => $countLimitsOnModeration > 0 ? true : false,
            ],
        ]);
    }

    /**
     * @param Card     $card
     * @param FuelType $type
     *
     * @return array
     * @throws \Exception
     */
    private function getCardLimits(Card $card, FuelType $type): array
    {
        $clientId = $card->getClient1CId();
        $cardNumber = $card->getCardNumber();

        $criteria = [
            'cardNumber_equalTo' => $cardNumber,
            FuelLimitByType::class => $type,
            'purseActivity_equalTo' => PurseActivity::active()->getValue(),
        ];

        $dayTransactionsSum = $this->transactionRepository->calculateDebitSum(
            $clientId,
            $cardNumber,
            new \DateTimeImmutable('today'),
            new \DateTimeImmutable('tomorrow')
        );

        $weekTransactionsSum = $this->transactionRepository->calculateDebitSum(
            $clientId,
            $cardNumber,
            new \DateTimeImmutable('monday this week'),
            new \DateTimeImmutable('monday next week')
        );

        $date = new \DateTimeImmutable('today');
        $monthTransactionsSum = $this->transactionRepository->calculateDebitSum(
            $clientId,
            $cardNumber,
            new \DateTimeImmutable($date->format('Y-m-01')),
            new \DateTimeImmutable($date->format('Y-m-t'))
        );

        $moneyLimits = [
            'name' => 'Гривня',
            'day' => [
                'total' => $card->getDayLimit(),
                'left' => $card->getDayLimit() - $dayTransactionsSum,
            ],
            'week' => [
                'total' => $card->getWeekLimit(),
                'left' => $card->getWeekLimit() - $weekTransactionsSum,
            ],
            'month' => [
                'total' => $card->getMonthLimit(),
                'left' => $card->getMonthLimit() - $monthTransactionsSum,
            ],
        ];

        $fuelTypes = $this->fuelTypeRepository->findMany([
            IndexByFuelCode::class => true,
        ]);

        $result = $this->cardLimitsRepository->findMany($criteria);

        $collection = [];
        /** @var FuelLimit $fuelLimit */
        foreach ($result as $fuelLimit) {
            $dayTransactionsSum = $this->transactionRepository->calculateFuelQuantitySum(
                $clientId,
                $cardNumber,
                $fuelLimit->getFuelCode(),
                new \DateTimeImmutable('today'),
                new \DateTimeImmutable('tomorrow')
            );

            $weekTransactionsSum = $this->transactionRepository->calculateFuelQuantitySum(
                $clientId,
                $cardNumber,
                $fuelLimit->getFuelCode(),
                new \DateTimeImmutable('monday this week'),
                new \DateTimeImmutable('monday next week')
            );

            $monthTransactionsSum = $this->transactionRepository->calculateFuelQuantitySum(
                $clientId,
                $cardNumber,
                $fuelLimit->getFuelCode(),
                new \DateTimeImmutable($date->format('Y-m-01')),
                new \DateTimeImmutable($date->format('Y-m-t'))
            );

            $collection[] = [
                'name' => $fuelTypes[$fuelLimit->getFuelCode()]->getFuelName(),
                'day' => [
                    'total' => $fuelLimit->getDayLimit(),
                    'left' => $fuelLimit->getDayLimit() - $dayTransactionsSum,
                ],
                'week' => [
                    'total' => $fuelLimit->getWeekLimit(),
                    'left' => $fuelLimit->getWeekLimit() - $weekTransactionsSum,
                ],
                'month' => [
                    'total' => $fuelLimit->getMonthLimit(),
                    'left' => $fuelLimit->getMonthLimit() - $monthTransactionsSum,
                ],
            ];
        }

        return [
            'limits' => $collection,
            'moneyLimits' => $moneyLimits,
        ];
    }
}
