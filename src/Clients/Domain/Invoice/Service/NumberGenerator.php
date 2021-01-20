<?php

namespace App\Clients\Domain\Invoice\Service;

interface NumberGenerator
{
    public function next(): string;
}
