<?php

declare(strict_types=1);

namespace CrudBundle\Interfaces;

interface Response
{
    public function getData(): array;

    public function getResult();

    public function hasErrors(): bool;

    public function getErrors(): array;

    public function toArray(): array;
}
