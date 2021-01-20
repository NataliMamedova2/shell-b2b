<?php

namespace App\Api\Crud\Interfaces;

use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

interface Response
{
    public function createErrorResponse($errors): SymfonyResponse;

    public function createSuccessResponse($data): SymfonyResponse;
}
