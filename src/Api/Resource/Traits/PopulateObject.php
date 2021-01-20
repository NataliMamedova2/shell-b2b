<?php

namespace App\Api\Resource\Traits;

trait PopulateObject
{
    public function populateObject(object $object)
    {
        $state = array_keys(get_object_vars($this));
        foreach ($state as $property) {
            $getter = 'get'.ucfirst($property);
            if (method_exists($object, $getter)) {
                $this->{$property} = $object->{$getter}();
            }
        }
    }
}
