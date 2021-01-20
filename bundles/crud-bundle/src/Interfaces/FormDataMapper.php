<?php

namespace CrudBundle\Interfaces;

interface FormDataMapper
{
    public function prepareDataToForm(object $data);
}
