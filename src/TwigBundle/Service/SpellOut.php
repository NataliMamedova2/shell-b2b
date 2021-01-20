<?php

namespace App\TwigBundle\Service;

interface SpellOut
{
    public function spellOut(string $number, string $locale = null): string;
}
