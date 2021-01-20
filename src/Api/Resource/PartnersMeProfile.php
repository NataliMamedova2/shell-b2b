<?php
namespace App\Api\Resource;

use App\Api\Resource\Traits\PopulateObject;
use App\Partners\Domain\Partner\Partner;
use App\Partners\Domain\User\User;
use App\Clients\Domain\User\ValueObject\Status;
use App\Media\Glide\Service\GlideUrlGenerator;
use App\Users\Domain\User\User as Manager;
use App\Users\Domain\User\ValueObject\Role;

final class PartnersMeProfile implements Model
{
    use PopulateObject;

    /**
     * @var GlideUrlGenerator
     */
    private $glideUrlGenerator;

    public function __construct(GlideUrlGenerator $glideUrlGenerator)
    {
        $this->glideUrlGenerator = $glideUrlGenerator;
    }

    public $username;
    public $email;
    public $firstName;
    public $lastName;
    public $middleName;
    public $phone;
    public $role;
    public $status;
    public $createdAt;
    public $manager;
    public $partner;
    public $client1C;

    public function prepare($data): Model
    {
        /** @var User $user */
        $user = $data['user'];
        $this->populateObject($user);

        $name = $user->getName();

        $this->firstName = $name->getFirstName();
        $this->lastName = $name->getLastName();
        $this->middleName = (string) $name->getMiddleName();

        $this->role = (new Role($user->getRole()))->getName();
        $this->status = (new Status($user->getStatus()))->getName();

        $this->partner = $this->preparePartner($data['partner']);
        $this->manager = $this->prepareManager($data['manager']);
        $this->client1C = $user->getPartner()->getClient1CId();

        return $this;
    }

    private function preparePartner(Partner $partner): array
    {
        return [
            'title' => $partner->getTitle(),
            'contractNumber' => $partner->getContractNumber(),
            'contractDate' => ($partner->getContractDate() instanceof \DateTimeInterface) ? $partner->getContractDate()->format('Y-m-d') : '',
        ];
    }

    private function prepareManager(?Manager $manager): ?array
    {
        if (empty($manager)) {
            return null;
        }

        $avatar = $manager->getAvatar();

        return [
            'name' => $manager->getName(),
            'phone' => $manager->getPhone(),
            'email' => $manager->getEmail(),
            'avatar' => $this->glideUrlGenerator->generate($avatar->getFile(), $avatar->getCropData()),
        ];
    }
}
