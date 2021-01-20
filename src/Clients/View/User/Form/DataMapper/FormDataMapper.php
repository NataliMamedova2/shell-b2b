<?php

namespace App\Clients\View\User\Form\DataMapper;

use App\Clients\Domain\User\ValueObject\Role;
use App\Clients\Domain\User\ValueObject\Status;

final class FormDataMapper implements \CrudBundle\Interfaces\FormDataMapper
{
    public function prepareDataToForm(object $data)
    {
        $roleNames = Role::getNames();

        return [
            'id' => $data->getId(),
            'username' => $data->getUsername(),
            'email' => $data->getEmail(),
            'firstName' => $data->getName()->getFirstName(),
            'middleName' => $data->getName()->getMiddleName(),
            'lastName' => $data->getName()->getLastName(),
            'phone' => $data->getPhone(),
            'role' => $roleNames[$data->getRole()],
        ];
    }
}
