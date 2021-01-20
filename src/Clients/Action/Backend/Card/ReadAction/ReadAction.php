<?php

namespace App\Clients\Action\Backend\Card\ReadAction;

use App\Application\Domain\ValueObject\ExportStatus;
use App\Application\Infrastructure\Criteria\ExportStatusCriteria;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\ValueObject\CardStatus;
use App\Clients\Domain\Card\ValueObject\ServiceSchedule;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Domain\FuelLimit\ValueObject\PurseActivity;
use App\Clients\Infrastructure\Fuel\Criteria\FuelLimitByType;
use App\Clients\Infrastructure\Fuel\Criteria\FuelNameOrder;
use App\Clients\Infrastructure\Fuel\Criteria\IndexByFuelCode;
use CrudBundle\Action\Response;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class ReadAction
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
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(
        Repository $cardRepository,
        Repository $cardLimitsRepository,
        Repository $fuelTypeRepository,
        AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->cardRepository = $cardRepository;
        $this->cardLimitsRepository = $cardLimitsRepository;
        $this->fuelTypeRepository = $fuelTypeRepository;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function __invoke(string $id): Response
    {
        $card = $this->cardRepository->find([
            'id_equalTo' => $id,
            'status_equalTo' => CardStatus::active()->getValue(),
        ]);

        if (!$card instanceof Card) {
            throw new NotFoundHttpException();
        }

        if (false === $this->authorizationChecker->isGranted('view', $card)) {
            throw new AccessDeniedException('Access Denied');
        }

        $cardNumber = $card->getCardNumber();
        $fuelTypes = $this->fuelTypeRepository->findMany([
            IndexByFuelCode::class => true,
        ]);

        $allFuelLimits = $this->cardLimitsRepository->findMany([
            'cardNumber_equalTo' => $cardNumber,
            'purseActivity_equalTo' => PurseActivity::active()->getValue(),
            FuelLimitByType::class => FuelType::fuel()->getValue(),
        ], [FuelNameOrder::class => 'ASC']);

        $fuelLimits = [];
        foreach ($allFuelLimits as $limit) {
            $fuelLimits[] = [
                'id' => $fuelTypes[$limit->getFuelCode()],
                'dayLimit' => $limit->getDayLimit() / 100,
                'monthLimit' => $limit->getMonthLimit() / 100,
                'weekLimit' => $limit->getWeekLimit() / 100,
            ];
        }

        $allGoodsLimits = $this->cardLimitsRepository->findMany([
            'cardNumber_equalTo' => $cardNumber,
            'purseActivity_equalTo' => PurseActivity::active()->getValue(),
            FuelLimitByType::class => FuelType::goods()->getValue(),
        ], [FuelNameOrder::class => 'ASC']);

        $godsLimits = [];
        foreach ($allGoodsLimits as $limit) {
            $godsLimits[] = [
                'id' => $fuelTypes[$limit->getFuelCode()],
                'dayLimit' => $limit->getDayLimit() / 100,
                'monthLimit' => $limit->getMonthLimit() / 100,
                'weekLimit' => $limit->getWeekLimit() / 100,
            ];
        }

        $allServicesLimits = $this->cardLimitsRepository->findMany([
            'cardNumber_equalTo' => $cardNumber,
            'purseActivity_equalTo' => PurseActivity::active()->getValue(),
            FuelLimitByType::class => FuelType::service()->getValue(),
        ], [FuelNameOrder::class => 'ASC']);

        $servicesLimits = [];
        foreach ($allServicesLimits as $limit) {
            $servicesLimits[] = [
                'id' => $fuelTypes[$limit->getFuelCode()],
                'dayLimit' => $limit->getDayLimit() / 100,
                'monthLimit' => $limit->getMonthLimit() / 100,
                'weekLimit' => $limit->getWeekLimit() / 100,
            ];
        }

        $days = ServiceSchedule::getNames();

        $daySchedule = (string) $card->getServiceSchedule();
        $scheduleDays = [];
        foreach (str_split($daySchedule) as $k => $value) {
            if ('1' === (string) $value) {
                $scheduleDays[] = $days[$k];
            }
        }

        $countLimitsOnModeration = $this->cardLimitsRepository->count([
            'cardNumber_equalTo' => $cardNumber,
            ExportStatusCriteria::class => ExportStatus::cantBeEditedStatuses(),
        ]);

        return new Response([
            'result' => [
                'card' => $card,
                'totalLimits' => [
                    'day' => $card->getDayLimit() / 100,
                    'week' => $card->getWeekLimit() / 100,
                    'month' => $card->getMonthLimit() / 100,
                ],
                'startUseTime' => $card->getTimeUseFrom(),
                'endUseTime' => $card->getTimeUseTo(),
                'serviceDays' => $scheduleDays,
                'fuelLimits' => $fuelLimits,
                'goodsLimits' => $godsLimits,
                'servicesLimits' => $servicesLimits,
                'haveLimitsOnModeration' => $countLimitsOnModeration > 0 ? true : false,
            ],
        ]);
    }
}
