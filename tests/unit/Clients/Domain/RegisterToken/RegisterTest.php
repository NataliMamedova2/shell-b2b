<?php

namespace Tests\Unit\Clients\Domain\RegisterToken;

use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\Email;
use App\Clients\Domain\Client\Client;
use App\Clients\Domain\Client\ValueObject\Agent1CId;
use App\Clients\Domain\Client\ValueObject\ClientId;
use App\Application\Domain\ValueObject\FcCbrId;
use App\Clients\Domain\Client\ValueObject\EdrpouInn;
use App\Clients\Domain\Client\ValueObject\FullName;
use App\Clients\Domain\Client\ValueObject\Manager1CId;
use App\Clients\Domain\Client\ValueObject\NktId;
use App\Clients\Domain\Client\ValueObject\Status;
use App\Clients\Domain\Client\ValueObject\Type;
use App\Clients\Domain\RegisterToken\Register;
use App\Clients\Domain\RegisterToken\ValueObject\RegisterId;
use App\Clients\Domain\RegisterToken\ValueObject\Token;
use PHPUnit\Framework\TestCase;

final class RegisterTest extends TestCase
{
    public function testCreate(): void
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = RegisterId::fromString($string);

        $client = $this->createClient();

        $email = new Email('test@email.com');
        $token = new Token('securetoken');

        $entity = Register::create(
            $identity,
            $client,
            $email,
            $token,
            new \DateTimeImmutable()
        );

        $this->assertEquals($string, $entity->getId());
        $this->assertEquals($client, $entity->getClient());
        $this->assertEquals($email, $entity->getEmail());
        $this->assertEquals('securetoken', $entity->getToken()->getToken());
        $this->assertEquals(false, $entity->getToken()->isExpiredTo(new \DateTimeImmutable()));
        $this->assertEquals(true, $entity->getToken()->isExpiredTo(new \DateTimeImmutable('+8 days')));
    }

    private function createClient(): Client
    {
        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = ClientId::fromString($string);

        $clientId = new Client1CId('КВ-0004888');
        $fullName = new FullName('Лавров Олександр Генадійович');
        $edrpouInn = new EdrpouInn('24584810');
        $type = new Type(0);
        $nktId = new NktId(9180004888);
        $managerId = new Manager1CId('КВЦ0000161');
        $agentId = new Agent1CId('');
        $fcCbrId = new FcCbrId(12435);
        $status = new Status(1);

        $dateTime = new \DateTimeImmutable('2019-01-01 00:00:00');
        $entity = Client::create(
            $identity,
            $clientId,
            $fullName,
            $edrpouInn,
            $type,
            $nktId,
            $managerId,
            $agentId,
            $fcCbrId,
            $status,
            $dateTime
        );

        return $entity;
    }

    public static function validEntity(): Register
    {
        $self = new static();

        $string = '550e8400-e29b-41d4-a716-446655440000';
        $identity = RegisterId::fromString($string);

        $client = $self->createClient();

        $email = new Email('test@email.com');
        $token = new Token('securetoken');

        $entity = Register::create(
            $identity,
            $client,
            $email,
            $token,
            new \DateTimeImmutable()
        );

        return $entity;
    }
}
