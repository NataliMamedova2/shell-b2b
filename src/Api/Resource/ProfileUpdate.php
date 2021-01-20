<?php

namespace App\Api\Resource;

use App\Api\Resource\Traits\PopulateObject;
use App\Clients\Domain\User\User;
use App\Clients\Domain\User\ValueObject\Status;
use App\Users\Domain\User\ValueObject\Role;

final class ProfileUpdate implements Model
{
    use PopulateObject;

    public $username;
    public $email;
    public $firstName;
    public $lastName;
    public $middleName;
    public $phone;
    public $role;
    public $status;
    public $createdAt;

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

        return $this;
    }
}
