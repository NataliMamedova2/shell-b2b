<?php

namespace CrudBundle\Action;

final class EmptyAction
{
    public function __invoke()
    {
        return new Response([]);
    }
}
