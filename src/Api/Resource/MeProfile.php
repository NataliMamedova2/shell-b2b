<?php

namespace App\Api\Resource;

use App\Api\Resource\Traits\PopulateObject;
use App\Clients\Domain\Company\Company;
use App\Clients\Domain\User\User;
use App\Clients\Domain\User\ValueObject\Status;
use App\Media\Glide\Service\GlideUrlGenerator;
use App\Users\Domain\User\User as Manager;
use App\Users\Domain\User\ValueObject\Role;

final class MeProfile implements Model
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
    public $company;

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

        $this->company = $this->prepareCompany($data['company']);
        $this->manager = $this->prepareManager($data['manager']);

        return $this;
    }

    private function prepareCompany(Company $company): array
    {
        $client = $company->getClient();
        return [
            'name' => $company->getName(),
            'contractNumber' => $client->getContractNumber(),
            'contractDate' => ($client->getContractDate() instanceof \DateTimeInterface) ? $client->getContractDate()->format('Y-m-d') : '',
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
