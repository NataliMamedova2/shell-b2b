<?php

namespace CrudBundle\Interfaces;

interface TargetRoute
{
    public function save(array $params = []): void;

    public function has(string $route): bool;

    public function get(string $route): array;
}
