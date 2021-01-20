<?php

namespace App\TwigBundle\Extension;

use App\TwigBundle\Service\AmountSpellOut;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

final class SpellOutExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('spellout_amount', [new AmountSpellOut(), 'spellOut']),
        ];
    }
}
