<?php

namespace App\Partners\Action\Command;

use App\Application\Domain\ValueObject\CardNumber;
use App\Application\Domain\ValueObject\Client1CId;
use App\Application\Domain\ValueObject\Email;
use App\Application\Domain\ValueObject\FuelCode;
use App\Application\Domain\ValueObject\Phone;
use App\Clients\Domain\Transaction\Card\ValueObject\AzcCode;
use App\Clients\Domain\Transaction\Card\ValueObject\AzcName;
use App\Clients\Domain\Transaction\Card\ValueObject\Debit;
use App\Clients\Domain\Transaction\Card\ValueObject\FuelQuantity;
use App\Clients\Domain\Transaction\Card\ValueObject\RegionCode;
use App\Clients\Domain\Transaction\Card\ValueObject\RegionName;
use App\Clients\Domain\Transaction\Card\ValueObject\StellaPrice;
use App\Clients\Domain\Transaction\Card\ValueObject\Transaction1CId;
use App\Clients\Domain\Transaction\Card\ValueObject\TransactionId;
use App\Clients\Domain\Transaction\Card\ValueObject\Type;
use App\Clients\Domain\User\Service\PasswordEncoder;
use App\Clients\Domain\User\ValueObject\Name;
use App\Clients\Domain\User\ValueObject\Username;
use App\Partners\Domain\Partner\Partner;
use App\Partners\Domain\Partner\ValueObject\Balance;
use App\Partners\Domain\Partner\ValueObject\CreditLimit;
use App\Partners\Domain\Partner\ValueObject\Edrpou;
use App\Partners\Domain\Partner\ValueObject\EmitentNumber;
use App\Partners\Domain\Partner\ValueObject\PartnerId;
use App\Partners\Domain\Partner\ValueObject\Title;
use App\Partners\Domain\Transaction\Transaction;
use App\Partners\Domain\Transaction\ValueObject\ClientPrice;
use App\Partners\Domain\Transaction\ValueObject\ClientSum;
use App\Partners\Domain\User\User;
use App\Partners\Domain\User\ValueObject\Role;
use App\Partners\Domain\User\ValueObject\UserId;
use Doctrine\Common\Persistence\ObjectManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreatePartners extends Command
{
    protected static $defaultName = 'create:partner';

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /** @var PasswordEncoder */
    private $passwordEncoder;

    public function __construct(ObjectManager $objectManager, PasswordEncoder $passwordEncoder)
    {
        $this->objectManager = $objectManager;
        $this->passwordEncoder = $passwordEncoder;
        parent::__construct();
    }

    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->createUsersAndPartners();
            $this->createTransaction();

            $this->objectManager->flush();
        } catch (\Exception $exception) {
            $output->writeln($exception->getMessage());
        }

        return 0;
    }

    private function createUsersAndPartners()
    {
        $users = [
            [
                'email' => new Email('test2@gmail.com'),
                'user' => new Username('user1'),
                'password' => '123456',
                'name' => new Name('firstName', 'middleName', 'lastName'),
                'phone' => new Phone('+380956400658'),
            ],
            [
                'email' => new Email('test@gmail.com'),
                'user' => new Username('user2'),
                'password' => '123456',
                'name' => new Name('firstName2', 'middleName2', 'lastName2'),
                'phone' => new Phone('+380986400658'),
            ],
        ];

        $partners = $this->createPartners();
        if (null !== $partners && true !== empty($partners)) {
            foreach ($partners as $key => $partner) {
                $user = $this->createUsers($partner, $users[$key]);
            }
        }
    }

    private function createTransaction()
    {
        $transactions = [
            [
                'id' => TransactionId::fromString(Uuid::uuid4()->toString()),
                'transactionId' => new Transaction1CId('29063795BB1E1F82AB03000381713F36'),
                'clientId1C' => new Client1CId('КВ-0001327'),
                'cardNumber' => new CardNumber('467020770'),
                'fuelCode' => new FuelCode('КВЦ0000008'),
                'fuelQuantity' => new FuelQuantity('10000'),
                'stellaPrice' => new StellaPrice('2449'),
                'debit' => new Debit('244900'),
                'clientPrice' => new ClientPrice('2074'),
                'clientSum' => new ClientSum('207400'),
                'azcCode' => new AzcCode('КВЦ0000592'),
                'azcName' => new AzcName('АЗС №R9013 Харківська обл., смт.Пісочин, вул.Окружна, 1'),
                'regionCode' => new RegionCode('КВЦ0000045'),
                'regionName' => new RegionName('Харків АХУ'),
                'postDate' => \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-09-11 07:57:58'),
                'type' => new Type('0'),
            ],
            [
                'id' => TransactionId::fromString(Uuid::uuid4()->toString()),
                'transactionId' => new Transaction1CId('AEFC0EEDCB034042AF29FD51705E156D'),
                'clientId1C' => new Client1CId('КВ-0000756'),
                'cardNumber' => new CardNumber('486070276'),
                'fuelCode' => new FuelCode('КВЦ0000009'),
                'fuelQuantity' => new FuelQuantity('1685'),
                'stellaPrice' => new StellaPrice('1229'),
                'debit' => new Debit('20709'),
                'clientPrice' => new ClientPrice('1129'),
                'clientSum' => new ClientSum('20709'),
                'azcCode' => new AzcCode('КВЦ0000592'),
                'azcName' => new AzcName('АЗС №R9013 Харківська обл., смт.Пісочин, вул.Окружна, 1'),
                'regionCode' => new RegionCode('КВЦ0000045'),
                'regionName' => new RegionName('Харків АХУ'),
                'postDate' => \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2020-09-11 00:19:33'),
                'type' => new Type('0'),
            ],
        ];
        foreach ($transactions as $transaction) {
            $newTransaction = Transaction::create(
                $transaction['id'],
                $transaction['transactionId'],
                $transaction['clientId1C'],
                $transaction['cardNumber'],
                $transaction['fuelCode'],
                $transaction['fuelQuantity'],
                $transaction['stellaPrice'],
                $transaction['debit'],
                $transaction['clientPrice'],
                $transaction['clientSum'],
                $transaction['azcCode'],
                $transaction['azcName'],
                $transaction['regionCode'],
                $transaction['regionName'],
                $transaction['postDate'],
                $transaction['type'],
                new \DateTimeImmutable()
            );
            $this->objectManager->persist($newTransaction);
        }
    }

    private function createPartners()
    {
        $partners = [];
        $id = PartnerId::fromString(Uuid::uuid4()->toString());
        $title = new Title('ГЕПАРД ОЙЛ ТОВ');
        $client1CId = new Client1CId('КВ-0001327');
        $edrpou = new Edrpou('467020199');
        $manager1CId = new \App\Partners\Domain\Partner\ValueObject\Manager1CId('КВЦ0000592');
        $emitentNumber = new EmitentNumber('0770');
        $balance = new Balance('1000');
        $creditLimit = new CreditLimit('180');

        $partner = Partner::create($id, $client1CId, $title, $edrpou, $manager1CId, $emitentNumber, $balance, $creditLimit, new \DateTimeImmutable());
        $this->objectManager->persist($partner);
        $partners[] = $partner;

        $id = PartnerId::fromString(Uuid::uuid4()->toString());
        $title = new Title('ВОГ КАРД ТОВ');
        $client1CId = new Client1CId('КВ-0000756');
        $edrpou = new Edrpou('467030199');
        $manager1CId = new \App\Partners\Domain\Partner\ValueObject\Manager1CId('КВЦ0000592');
        $emitentNumber = new EmitentNumber('0276');
        $balance = new Balance('2000');
        $creditLimit = new CreditLimit('140');

        $partner = Partner::create($id, $client1CId, $title, $edrpou, $manager1CId, $emitentNumber, $balance, $creditLimit, new \DateTimeImmutable());
        $this->objectManager->persist($partner);

        $partners[] = $partner;

        return $partners;
    }

    private function createUsers(Partner $partner, array $user)
    {
        $id = UserId::fromString(Uuid::uuid4()->toString());
        $role = Role::fromName('partner');
        $passwordHash = $this->passwordEncoder->encode($user['password']);
        $user = User::create(
            $id,
            $partner,
            $user['email'],
            $user['user'],
            $passwordHash,
            $user['name'],
            $role,
            $user['phone']
        );
        $this->objectManager->persist($user);

        return $user;
    }
}
