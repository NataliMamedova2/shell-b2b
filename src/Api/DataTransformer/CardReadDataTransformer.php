<?php

namespace App\Api\DataTransformer;

use App\Api\Crud\Interfaces\DataTransformer;
use App\Api\Resource\FuelCardRead;
use App\Application\Domain\ValueObject\ExportStatus;
use App\Application\Infrastructure\Criteria\ExportStatusCriteria;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Domain\FuelLimit\ValueObject\PurseActivity;
use App\Clients\Infrastructure\Fuel\Criteria\FuelLimitByType;
use App\Clients\Infrastructure\Fuel\Criteria\FuelNameOrder;
use App\Clients\Infrastructure\Fuel\Criteria\IndexByFuelCode;
use App\Security\Cabinet\MyselfInterface;
use Infrastructure\Interfaces\Repository\Repository;

final class CardReadDataTransformer implements DataTransformer
{
    /**
     * @var Repository
     */
    private $fuelCardLimitRepository;

    /**
     * @var Repository
     */
    private $fuelTypeRepository;

    /**
     * @var MyselfInterface
     */
    private $myself;

    public function __construct(
        Repository $fuelCardLimitRepository,
        Repository $fuelTypeRepository,
        MyselfInterface $myself
    ) {
        $this->fuelCardLimitRepository = $fuelCardLimitRepository;
        $this->fuelTypeRepository = $fuelTypeRepository;
        $this->myself = $myself;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($card)
    {
        $cardNumber = $card->getCardNumber();
        $client = $this->myself->getClient();

        $countLimitsOnModeration = $this->fuelCardLimitRepository->count([
            'cardNumber_equalTo' => $cardNumber,
            'client1CId_equalTo' => $client->getClient1CId(),
            ExportStatusCriteria::class => ExportStatus::cantBeEditedStatuses(),
        ]);

        $fuelLimits = $this->fuelCardLimitRepository->findMany([
            'cardNumber_equalTo' => $cardNumber,
            'client1CId_equalTo' => $client->getClient1CId(),
            'purseActivity_equalTo' => PurseActivity::active()->getValue(),
            FuelLimitByType::class => FuelType::fuel()->getValue(),
        ], [FuelNameOrder::class => 'ASC']);

        $goodsLimits = $this->fuelCardLimitRepository->findMany([
            'cardNumber_equalTo' => $cardNumber,
            'client1CId_equalTo' => $client->getClient1CId(),
            'purseActivity_equalTo' => PurseActivity::active()->getValue(),
            FuelLimitByType::class => FuelType::goods()->getValue(),
        ], [FuelNameOrder::class => 'ASC']);

        $servicesLimits = $this->fuelCardLimitRepository->findMany([
            'cardNumber_equalTo' => $cardNumber,
            'client1CId_equalTo' => $client->getClient1CId(),
            'purseActivity_equalTo' => PurseActivity::active()->getValue(),
            FuelLimitByType::class => FuelType::service()->getValue(),
        ], [FuelNameOrder::class => 'ASC']);

        /** @var Type[] $fuelTypes */
        $fuelTypes = $this->fuelTypeRepository->findMany([
            IndexByFuelCode::class => true,
        ]);

        $model = new FuelCardRead($fuelLimits, $goodsLimits, $servicesLimits, $fuelTypes, ($countLimitsOnModeration > 0));

        return $model->prepare($card);
    }
}
