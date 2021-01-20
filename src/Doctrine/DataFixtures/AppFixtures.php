<?php

namespace App\Doctrine\DataFixtures;

use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\Card\Card;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Client\ValueObject\Manager1CId;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\User\User as CabinetUser;
use App\Clients\Domain\User\ValueObject\Name;
use App\Users\Domain\User\User as BackendUser;
use App\Users\Domain\User\ValueObject\Avatar;
use App\Users\Domain\User\ValueObject\FullName;
use App\Users\Domain\User\ValueObject\Phone;
use App\Users\Domain\User\ValueObject\Role;
use App\Users\Domain\User\ValueObject\Status;
use App\Users\Domain\User\ValueObject\Username;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use League\FactoryMuffin\FactoryMuffin;

class AppFixtures extends Fixture
{
    /**
     * @var FactoryMuffin
     */
    private $factoryMuffin;
    /**
     * @var ObjectManager
     */
    private $manager;

    public function __construct(FactoryMuffin $factoryMuffin)
    {
        $this->factoryMuffin = $factoryMuffin;
    }

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->factoryMuffin->loadFactories(__DIR__.'/../../tests/fixtures');

        $this->cabinetFixture();

        $this->factoryMuffin->seed(5, BackendUser::class);
        $this->factoryMuffin->seed(15, Client::class);
    }

    private function cabinetFixture()
    {
        /** @var Company $company */
        $company = $this->factoryMuffin->create(Company::class);

        $client1CId = $company->getClient()->getClient1CId();

        $this->createCabinetUser([
            'username' => 'admin',
            'email' => 'admin@email.com',
            'role' => \App\Clients\Domain\User\ValueObject\Role::fromName('admin'),
        ], $company);
        $this->createCabinetUser([
            'username' => 'johndou',
            'email' => 'john@email.com',
            'role' => \App\Clients\Domain\User\ValueObject\Role::fromName('admin'),
        ], $company);
        $this->createCabinetUser([
            'username' => 'manager',
            'email' => 'manager@email.com',
            'role' => \App\Clients\Domain\User\ValueObject\Role::fromName('manager'),
        ], $company);
        $this->createCabinetUser([
            'username' => 'accountant',
            'email' => 'accountant@email.com',
            'role' => \App\Clients\Domain\User\ValueObject\Role::fromName('accountant'),
        ], $company);

        for ($i = 0; $i < 15; ++$i) {
            $this->createCard($client1CId);
        }

        $this->manager->flush();
    }

    private function createBackendUser(array $data = [])
    {
        if (!empty($data)) {
            $this->factoryMuffin->getDefinition(BackendUser::class)
                ->setCallback(function ($object, $saved) use ($data) {
                    /* @var BackendUser $object */
                    $object->update(
                        new Email($data['email']),
                        new Username($data['username']),
                        new FullName($data['fullName']),
                        new Role($data['role']),
                        new Status($data['status']),
                        new Phone($data['phone']),
                        new Avatar($data['path'], $data['fileName'], $data['cropData']),
                        new Manager1CId($data['manager1cId'])
                    );
                });
        }

        return $this->factoryMuffin->create(BackendUser::class);
    }

    private function createCabinetUser(array $data = [], Company $company = null)
    {
        if (!empty($data) || !empty($company)) {
            $this->factoryMuffin->getDefinition(CabinetUser::class)
                /* @var CabinetUser $object */
                ->setCallback(function ($object, $saved) use ($data, $company) {
                    $default = [
                        'email' => $object->getEmail(),
                        'username' => $object->getUsername(),
                        'firstName' => $object->getName()->getFirstName(),
                        'middleName' => $object->getName()->getMiddleName(),
                        'lastName' => $object->getName()->getLastName(),
                        'role' => $object->getRole(),
                        'phone' => $object->getPhone(),
                    ];

                    $data = array_merge($default, $data);

                    $object->update(
                        new Email($data['email']),
                        new \App\Clients\Domain\User\ValueObject\Username($data['username']),
                        new Name($data['firstName'], $data['middleName'], $data['lastName']),
                        new \App\Clients\Domain\User\ValueObject\Role($data['role']),
                        new \App\Application\Domain\ValueObject\Phone($data['phone'])
                    );

                    if ($company instanceof Company) {
                        $this->invokeProperty($object, 'company', $company);
                    }
                });
        }

        return $this->factoryMuffin->create(CabinetUser::class);
    }

    private function createCard(string $clientId = null)
    {
        /** @var Card $card */
        $card = $this->factoryMuffin->instance(Card::class);

        if (null !== $clientId) {
            $this->invokeProperty($card, 'client1CId', $clientId);
        }

        $this->manager->persist($card);

        return $card;
    }

    private function invokeProperty(&$object, $propertyName, $value)
    {
        $reflection = new \ReflectionClass(get_class($object));
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);

        $property->setValue($object, $value);

        return $object;
    }
}
