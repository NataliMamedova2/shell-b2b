<?php

namespace Tests\Helper;

use App\Application\Domain\ValueObject\CardNumber;
use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\FuelCode;
use App\Application\Domain\ValueObject\Phone;
use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Card\StopList;
use App\Clients\Domain\Card\ValueObject\CardStatus;
use App\Clients\Domain\Card\ValueObject\CarNumber;
use App\Clients\Domain\Card\ValueObject\DayLimit;
use App\Clients\Domain\Card\ValueObject\MonthLimit;
use App\Clients\Domain\Card\ValueObject\ServiceSchedule;
use App\Clients\Domain\Card\ValueObject\TimeUse;
use App\Clients\Domain\Card\ValueObject\WeekLimit;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Client\ValueObject\Agent1CId;
use App\Clients\Domain\Client\ValueObject\EdrpouInn;
use App\Clients\Domain\Client\ValueObject\FullName;
use App\Clients\Domain\Client\ValueObject\Manager1CId;
use App\Clients\Domain\Client\ValueObject\NktId;
use App\Clients\Domain\Client\ValueObject\Status;
use App\Clients\Domain\Client\ValueObject\Type;
use App\Clients\Domain\ClientInfo\ClientInfo;
use App\Clients\Domain\ClientInfo\ValueObject\Balance;
use App\Clients\Domain\ClientInfo\ValueObject\CreditLimit;
use App\Clients\Domain\ClientInfo\ValueObject\LastTransactionDate;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\Company\ValueObject\Accounting;
use App\Clients\Domain\Company\ValueObject\PostalAddress;
use App\Clients\Domain\Discount\Discount;
use App\Clients\Domain\Discount\ValueObject\DiscountSum;
use App\Clients\Domain\Document\Document;
use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\Fuel\Price\Price;
use App\Clients\Domain\FuelLimit\FuelLimit;
use App\Clients\Domain\FuelLimit\ValueObject\PurseActivity;
use App\Clients\Domain\RefillBalance\RefillBalance;
use App\Clients\Domain\ShellInformation\ShellInformation;
use App\Clients\Domain\Transaction\Card\Transaction;
use App\Clients\Domain\Transaction\Card\ValueObject\AzcCode;
use App\Clients\Domain\Transaction\Card\ValueObject\AzcName;
use App\Clients\Domain\Transaction\Card\ValueObject\Debit;
use App\Clients\Domain\Transaction\Card\ValueObject\FuelQuantity;
use App\Clients\Domain\Transaction\Card\ValueObject\RegionCode;
use App\Clients\Domain\Transaction\Card\ValueObject\RegionName;
use App\Clients\Domain\Transaction\Card\ValueObject\StellaPrice;
use App\Clients\Domain\User\User;
use App\Clients\Domain\User\ValueObject\Name;
use App\Clients\Domain\User\ValueObject\Role;
use App\Clients\Domain\User\ValueObject\Username;
use App\Users\Domain\User\ValueObject\Avatar;
use Codeception\Module;
use Codeception\Module\Doctrine2;
use Codeception\TestCase;
use Faker\Factory;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Stores\RepositoryStore;

final class Factories extends Module
{
    /**
     * @var FactoryMuffin
     */
    protected $factory;
    /**
     * @var \Faker\Generator
     */
    private $faker;
    /**
     * @var RepositoryStore
     */
    private $store;

    public function _initialize()
    {
        if (null === $this->factory) {
            /** @var Doctrine2 $doctrine */
            $doctrine = $this->getModule('Doctrine2');
            $this->store = new RepositoryStore($doctrine->_getEntityManager());

            $this->factory = new FactoryMuffin($this->store);
            $this->factory->loadFactories(realpath(__DIR__).'/../../fixtures');
        }

        $this->faker = Factory::create();

        $filesystem = $this->getModule('Filesystem');
        $filesystem->copyDir('tests/_data/storage', 'storage/source/tests');
    }

    public function haveClient(array $data = []): Client
    {
        $this->factory->getDefinition(Client::class)
            ->setCallback(
                function ($object, $saved) use ($data) {
                    /* @var Client $object */
                    $default = $this->toArray($object);

                    $data = array_merge($default, $data);

                    $object->update(
                        new FullName($data['fullName']),
                        new EdrpouInn($data['edrpouInn']),
                        new Type($data['type']),
                        new NktId($data['nktId']),
                        new Manager1CId($data['manager1CId']),
                        new Agent1CId($data['agent1CId']),
                        new FcCbrId($data['fcCbrId']),
                        new Status($data['status'])
                    );
                }
            );

        return $this->factory->create(Client::class);
    }

    public function haveClientInfo(Client $client, array $data = []): ClientInfo
    {
        /** @var ClientInfo $entity */
        $entity = $this->factory->instance(ClientInfo::class);

        $this->invokeProperty($entity, 'fcCbrId', $client->getFcCbrId());
        $this->invokeProperty($entity, 'clientPcId', $client->getClientPcId());

        $default = $this->toArray($entity);
        $data = array_merge($default, $data);

        $lastTransactionDate = new LastTransactionDate(
            \DateTimeImmutable::createFromFormat('d/m/Y', '01/01/1900'),
            new \DateTimeImmutable('00:00:00')
        );

        $entity->update(
            new Balance($data['balance']),
            new CreditLimit($data['creditLimit']),
            $lastTransactionDate,
            new \DateTimeImmutable()
        );

        $this->store->persist($entity);

        return $entity;
    }

    public function haveCabinetUser(array $data = [], Company $company = null): User
    {
        $username = $this->faker->unique()->userName;
        if (strlen($username) < 5) {
            $username = $username.str_repeat('0', 5 - strlen($username));
        }
        $firstName = $this->faker->firstName;
        if (strlen($firstName) < 2) {
            $firstName = $firstName.str_repeat('a', 2 - strlen($firstName));
        }
        $lastName = $this->faker->lastName;
        if (strlen($lastName) < 2) {
            $lastName = $lastName.str_repeat('a', 2 - strlen($lastName));
        }

        $defaultData = [
            'email' => $this->faker->unique()->email,
            'username' => $username,
            'firstName' => $firstName,
            'middleName' => $firstName,
            'lastName' => $lastName,
            'role' => 'admin',
            'phone' => '+380500073400',
            'status' => 'active',
        ];
        $data = array_merge($defaultData, $data);

        $entity = $this->factory->instance(User::class);

        /* @var User $entity */
        $entity->update(
            new Email($data['email']),
            new Username($data['username']),
            new Name($data['firstName'], $data['middleName'], $data['lastName']),
            Role::fromName($data['role']),
            new Phone($data['phone'])
        );

        if (!empty($company)) {
            $this->invokeProperty($entity, 'company', $company);
        }

        $entity->changeStatus(\App\Clients\Domain\User\ValueObject\Status::fromName($data['status']));

        $this->store->persist($entity);

        return $entity;
    }

    public function haveCompany(array $data = []): Company
    {
        /** @var Company $entity */
        $entity = $this->factory->instance(Company::class);

        if (!empty($data)) {
            $defaultData = [
                'name' => $entity->getName(),
                'email' => $entity->getAccounting()->getEmail(),
                'phone' => $entity->getAccounting()->getPhone(),
                'postalAddress' => $entity->getPostalAddress(),
            ];
            $data = array_merge($defaultData, $data);

            $email = new Email($data['email']);
            $phone = new Phone($data['phone']);
            $entity->update(
                new \App\Clients\Domain\Company\ValueObject\Name($data['name']),
                new Accounting($email, $phone),
                new PostalAddress($data['postalAddress'])
            );
        }

        $this->store->persist($entity);

        return $entity;
    }

    public function haveFuelType(array $data = []): \App\Clients\Domain\Fuel\Type\Type
    {
        /** @var \App\Clients\Domain\Fuel\Type\Type $entity */
        $entity = $this->factory->instance(\App\Clients\Domain\Fuel\Type\Type::class);
        if (isset($data['fuelType'])) {
            $this->invokeProperty($entity, 'fuelType', $data['fuelType']);
        }

        if (isset($data['fuelCode'])) {
            $this->invokeProperty($entity, 'fuelCode', $data['fuelCode']);
        }

        if (isset($data['purseCode'])) {
            $this->invokeProperty($entity, 'purseCode', $data['purseCode']);
        }

        if (isset($data['fuelName'])) {
            $this->invokeProperty($entity, 'fuelName', $data['fuelName']);
        }

        $this->store->persist($entity);

        return $entity;
    }

    public function haveFuelList(int $count, array $data = []): array
    {
        $fuelFuelType = 1;
        $data = array_merge($data, ['fuelType' => $fuelFuelType]);

        $collection = [];
        for ($i = 0; $i < $count; ++$i) {
            $collection[] = $this->haveFuelType($data);
        }

        return $collection;
    }

    public function haveGoodsList(int $count, array $data = []): array
    {
        $goodsFuelType = 2;
        $data = array_merge($data, ['fuelType' => $goodsFuelType]);

        $collection = [];
        for ($i = 0; $i < $count; ++$i) {
            $collection[] = $this->haveFuelType($data);
        }

        return $collection;
    }

    public function haveServicesList(int $count, array $data = []): array
    {
        $serviceFuelType = 3;
        $data = array_merge($data, ['fuelType' => $serviceFuelType]);

        $collection = [];
        for ($i = 0; $i < $count; ++$i) {
            $collection[] = $this->haveFuelType($data);
        }

        return $collection;
    }

    public function haveFuelPrice(\App\Clients\Domain\Fuel\Type\Type $fuel, int $price = null): Price
    {
        /** @var Price $entity */
        $entity = $this->factory->instance(Price::class);

        $this->invokeProperty($entity, 'fuelCode', $fuel->getFuelCode());
        if (null !== $price) {
            $this->invokeProperty($entity, 'fuelPrice', $price);
        }

        $this->store->persist($entity);

        return $entity;
    }

    /**
     * @return Card[]
     */
    public function haveFuelCardList(int $count, array $data = []): array
    {
        $collection = [];
        for ($i = 0; $i < $count; ++$i) {
            $collection[] = $this->haveFuelCard($data);
        }

        return $collection;
    }

    public function haveFuelCard(array $data = [], Driver $driver = null): Card
    {
        /** @var Card $entity */
        $entity = $this->factory->instance(Card::class);

        if (!empty($data)) {
            /* @var Card $object */
            $default = $this->toArray($entity);
            $default['status'] = 'active';

            $data = array_merge($default, $data);

            $timeUse = new TimeUse($data['timeUseFrom'], $data['timeUseTo']);
            $entity->update(
                new Client1CId($data['client1CId']),
                new CarNumber($data['carNumber']),
                new DayLimit($data['dayLimit']),
                new WeekLimit($data['weekLimit']),
                new MonthLimit($data['monthLimit']),
                new ServiceSchedule($data['serviceSchedule']),
                $timeUse,
                CardStatus::fromName($data['status'])
            );
        }

        if (null !== $driver) {
            $entity->changeDriver($driver);
        }

        $this->store->persist($entity);

        return $entity;
    }

    public function haveFuelCardLimitsList(int $count, array $data = []): array
    {
        $collection = [];
        for ($i = 0; $i < $count; ++$i) {
            $collection[] = $this->haveFuelCardLimits($data);
        }

        return $collection;
    }

    public function haveFuelCardLimits(array $data = []): FuelLimit
    {
        /** @var FuelLimit $entity */
        $entity = $this->factory->instance(FuelLimit::class);

        if (!empty($data)) {
            $default = $this->toArray($entity);

            $data = array_merge($default, $data);

            $entity->update(
                new Client1CId($data['client1CId']),
                new FuelCode($data['fuelCode']),
                new \App\Clients\Domain\FuelLimit\ValueObject\DayLimit($data['dayLimit']),
                new \App\Clients\Domain\FuelLimit\ValueObject\WeekLimit($data['weekLimit']),
                new \App\Clients\Domain\FuelLimit\ValueObject\MonthLimit($data['monthLimit']),
                new PurseActivity($data['purseActivity'])
            );

            if (isset($data['cardNumber']) && !empty($data['cardNumber'])) {
                $this->invokeProperty($entity, 'cardNumber', $data['cardNumber']);
            }
        }

        $this->store->persist($entity);

        return $entity;
    }

    public function haveCardTransaction(array $data = []): Transaction
    {
        /** @var Transaction $entity */
        $entity = $this->factory->instance(Transaction::class);

        if (!empty($data)) {
            $default = $this->toArray($entity);

            $data = array_merge($default, $data);

            $entity->update(
                new Client1CId($data['client1CId']),
                new CardNumber($data['cardNumber']),
                new FuelCode($data['fuelCode']),
                new FuelQuantity($data['fuelQuantity']),
                new StellaPrice($data['stellaPrice']),
                new Debit($data['debit']),
                new AzcCode($data['azsCode']),
                new AzcName($data['azsName']),
                new RegionCode($data['regionCode']),
                new RegionName($data['regionName']),
                $data['postDate'],
                new \App\Clients\Domain\Transaction\Card\ValueObject\Type($data['type'])
            );
        }

        $this->store->persist($entity);

        return $entity;
    }

    public function haveCardTransactionList(int $count, array $data = []): array
    {
        $collection = [];
        for ($i = 0; $i < $count; ++$i) {
            $collection[] = $this->haveCardTransaction($data);
        }

        return $collection;
    }

    public function haveStopList(Card $card): StopList
    {
        /** @var StopList $stopList */
        $stopList = $this->factory->instance(StopList::class);

        $this->invokeProperty($stopList, 'card', $card);
        $this->store->persist($stopList);

        return $stopList;
    }

    public function haveStopListInProgressStatus(Card $card): StopList
    {
        /** @var StopList $stopList */
        $stopList = $this->factory->instance(StopList::class);
        $this->invokeProperty($stopList, 'card', $card);

        $stopList->getExportStatus()->inProgress();

        $this->store->persist($stopList);

        return $stopList;
    }

    public function haveShellInfo(): ShellInformation
    {
        return $this->factory->create(ShellInformation::class);
    }

    public function haveDocument(Client $client = null): Document
    {
        /** @var Document $entity */
        $entity = $this->factory->instance(Document::class);

        if (null !== $client) {
            $this->invokeProperty($entity, 'client1CId', $client->getClient1CId());
        }
        $this->store->persist($entity);

        return $entity;
    }

    public function haveRefillBalance(Client $client = null): RefillBalance
    {
        /** @var RefillBalance $entity */
        $entity = $this->factory->instance(RefillBalance::class);

        if (null !== $client) {
            $this->invokeProperty($entity, 'fcCbrId', $client->getClientPcId());
        }
        $this->store->persist($entity);

        return $entity;
    }

    public function haveDiscount(Client $client = null, array $data = []): Discount
    {
        /** @var Discount $entity */
        $entity = $this->factory->instance(Discount::class);

        $defaultData = [
            'sum' => $entity->getDiscountSum(),
            'date' => $entity->getOperationDate()->format('Y-m-d H:i:s'),
        ];

        $data = array_merge($defaultData, $data);

        $entity->update(
            new DiscountSum($data['sum']),
            new \DateTimeImmutable($data['date'])
        );

        if (null !== $client) {
            $this->invokeProperty($entity, 'client1CId', $client->getClient1CId());
        }
        $this->store->persist($entity);

        return $entity;
    }

    public function haveDiscountsList(Client $client, int $count): array
    {
        $collection = [];
        for ($i = 0; $i < $count; ++$i) {
            $collection[] = $this->haveDiscount($client);
        }

        return $collection;
    }

    public function haveDriver(Client $client = null): Driver
    {
        /** @var Driver $entity */
        $entity = $this->factory->instance(Driver::class);

        if (null !== $client) {
            $this->invokeProperty($entity, 'client1CId', $client->getClient1CId());
        }
        $this->store->persist($entity);

        return $entity;
    }

    public function haveManager(Client $client, array $data = []): \App\Users\Domain\User\User
    {
        /** @var \App\Users\Domain\User\User $entity */
        $entity = $this->factory->instance(\App\Users\Domain\User\User::class);

        $default = $this->toArray($entity);
        $data = array_merge($default, $data);

        $entity->update(
            new Email($data['email']),
            new \App\Users\Domain\User\ValueObject\Username($data['username']),
            new \App\Users\Domain\User\ValueObject\FullName($data['name']),
            \App\Users\Domain\User\ValueObject\Role::formName('manager'),
            \App\Users\Domain\User\ValueObject\Status::active(),
            new \App\Users\Domain\User\ValueObject\Phone('+380898881111'),
            new Avatar('tests/', 'chess.jpg'),
            new Manager1CId($client->getManager1CId())
        );

        $this->store->persist($entity);

        return $entity;
    }

    public function _after(TestCase $test)
    {
        $this->factory->deleteSaved();
    }

    private function toArray(object $object): array
    {
        $reflectionClass = new \ReflectionClass(get_class($object));
        $array = [];
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);

            $value = $property->getValue($object);

            if (is_object($value) && method_exists($value, '__toString')) {
                $value = (string) $value;
            }

            $array[$property->getName()] = $value;
            $property->setAccessible(false);
        }

        return $array;
    }

    private function invokeProperty(&$object, $propertyName, $value)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
        $property->setAccessible(false);

        return $object;
    }
}
