<?php

namespace App\Api\Resource;

interface Model
{
    public function prepare($data): ?self;
}
