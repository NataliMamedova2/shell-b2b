<?php

namespace App\Api\Resource;

use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\StopList;
use App\Clients\Domain\Card\ValueObject\CardStatus;
use App\Clients\Domain\Card\ValueObject\ServiceSchedule;
use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\FuelLimit\FuelLimit;

final class FuelCardRead implements Model
{
    use Traits\PopulateObject;

    public $id;
    public $cardNumber;
    public $status;
    public $onModeration = false;
    public $driver = null;
    public $totalLimits = [];
    public $startUseTime;
    public $endUseTime;
    public $serviceDays = [];
    public $fuelLimits = [];
    public $goodsLimits = [];
    public $servicesLimits = [];

    /**
     * @var Type[]
     */
    private $fuelTypes;
    /**
     * @var FuelLimit[]
     */
    private $fuel;
    /**
     * @var FuelLimit[]
     */
    private $goods;
    /**
     * @var FuelLimit[]
     */
    private $services;
    /**
     * @var StopList
     */
    private $stopList;
    /**
     * @var bool
     */
    private $fuelLimitOnModeration = false;

    public function __construct(array $fuel, array $goods, array $services, array $fuelTypes, bool $fuelLimitOnModeration = false)
    {
        $this->fuel = $fuel;
        $this->goods = $goods;
        $this->services = $services;
        $this->fuelTypes = $fuelTypes;
        $this->fuelLimitOnModeration = $fuelLimitOnModeration;
    }

    /**
     * @param Card $fuelCard
     *
     * @return Model
     */
    public function prepare($fuelCard): Model
    {
        $this->populateObject($fuelCard);

        $this->status = (new CardStatus($fuelCard->getStatus()))->getName();
        if (true === $fuelCard->getExportStatus()->onModeration() || true === $this->fuelLimitOnModeration) {
            $this->onModeration = true;
        }

        $this->totalLimits = [
            'day' => $fuelCard->getDayLimit(),
            'week' => $fuelCard->getWeekLimit(),
            'month' => $fuelCard->getMonthLimit(),
        ];

        $days = ServiceSchedule::getNames();

        $daySchedule = (string) $fuelCard->getServiceSchedule();
        $scheduleDays = [];
        foreach (str_split($daySchedule) as $k => $value) {
            if ('1' === (string) $value) {
                $scheduleDays[] = $days[$k];
            }
        }

        $this->startUseTime = $fuelCard->getTimeUseFrom()->format('H:i');
        $this->endUseTime = $fuelCard->getTimeUseTo()->format('H:i');
        $this->serviceDays = $scheduleDays;

        foreach ($this->fuel as $limit) {
            $fuelType = $this->getFuelByCode($limit->getFuelCode());
            $this->fuelLimits[] = [
                'id' => $fuelType->getId(),
                'name' => $fuelType->getFuelName(),
                'dayLimit' => $limit->getDayLimit(),
                'monthLimit' => $limit->getMonthLimit(),
                'weekLimit' => $limit->getWeekLimit(),
            ];
        }

        foreach ($this->goods as $limit) {
            $fuelType = $this->getFuelByCode($limit->getFuelCode());
            $this->goodsLimits[] = [
                'id' => $fuelType->getId(),
                'name' => $fuelType->getFuelName(),
                'dayLimit' => $limit->getDayLimit(),
                'monthLimit' => $limit->getMonthLimit(),
                'weekLimit' => $limit->getWeekLimit(),
            ];
        }

        foreach ($this->services as $limit) {
            $fuelType = $this->getFuelByCode($limit->getFuelCode());
            $this->servicesLimits[] = [
                'id' => $fuelType->getId(),
                'name' => $fuelType->getFuelName(),
                'dayLimit' => $limit->getDayLimit(),
                'monthLimit' => $limit->getMonthLimit(),
                'weekLimit' => $limit->getWeekLimit(),
            ];
        }

        $driver = $fuelCard->getDriver();
        if ($driver instanceof \App\Clients\Domain\Driver\Driver) {
            $driverResource = new DriverShort();
            $this->driver = $driverResource->prepare($driver);
        }

        return $this;
    }

    private function getFuelByCode(string $fuelCode): Type
    {
        return $this->fuelTypes[$fuelCode];
    }
}
