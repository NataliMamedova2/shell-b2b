<?php

use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Driver\Driver;
use App\Clients\Domain\Driver\ValueObject\CarNumber;
use App\Clients\Domain\Driver\ValueObject\DriverId;
use App\Clients\Domain\Driver\ValueObject\Name;
use App\Clients\Domain\Driver\ValueObject\Note;
use App\Clients\Domain\Driver\ValueObject\Status;
use League\FactoryMuffin\FactoryMuffin;
use League\FactoryMuffin\Faker\Facade as Faker;

/**
 * @var FactoryMuffin $fm
 */
$fm
    ->define(Driver::class)
    ->setMaker(
        function ($class) use ($fm) {
            /* @var Driver $class */

            /** @var Client $client */
            $client = $fm->instance(Client::class);

            $firstName = Faker::firstName()();
            $lastName = Faker::lastName()();

            $name = new Name($firstName, $firstName, $lastName);
            $phones = [
                '+380972342344',
                '+380632342300',
            ];
            $email = new Email(Faker::unique()->email()());
            $note = new Note('Note text');

            $entity = $class::create(
                DriverId::next(),
                $client,
                $name,
                Status::active(),
                $phones,
                new DateTimeImmutable(),
                $email,
                $note
            );

            $carsNumbers = ['AA12333', 'EM-12333', 'II9999'];
            foreach ($carsNumbers as $carsNumber) {
                $entity->addCarNumber(new CarNumber($carsNumber), new \DateTimeImmutable());
            }

            return $entity;
        }
    );
