<?php

namespace App\Api\Resource;

use App\Api\Resource\Traits\PopulateObject;
use App\Clients\Domain\User\User;
use App\Clients\Domain\User\ValueObject\Status;
use App\Users\Domain\User\ValueObject\Role;

final class CompanyUser implements Model
{
    use PopulateObject;

    public $id;
    public $username;
    public $email;
    public $firstName;
    public $lastName;
    public $middleName;
    public $phone;
    public $role;
    public $status;
    public $createdAt;
    public $lastLoggedAt;

    /**
     * @param User $user
     *
     * @return Model
     */
    public function prepare($user): Model
    {
        $this->populateObject($user);

        $name = $user->getName();

        $this->firstName = $name->getFirstName();
        $this->lastName = $name->getLastName();
        $this->middleName = (string) $name->getMiddleName();
        $this->role = (new Role($user->getRole()))->getName();
        $this->status = (new Status($user->getStatus()))->getName();

        $this->lastLoggedAt = ($user->getLastLoggedAt() instanceof \DateTimeInterface) ? $user->getLastLoggedAt() : '';

        return $this;
    }
}
