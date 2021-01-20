<?php

namespace Tests\Unit\TwigBundle\Extension;

use App\TwigBundle\Extension\SpellOutExtension;
use App\TwigBundle\Service\AmountSpellOut;
use PHPUnit\Framework\TestCase;

final class SpellOutExtensionTest extends TestCase
{

    public function testGetFilters()
    {
        $extension = new SpellOutExtension();

        $result = $extension->getFilters();

        $result = $result[0];

        $this->assertEquals('spellout_amount', $result->getName());
        $this->assertEquals([new AmountSpellOut(), 'spellOut'], $result->getCallable());

    }
}
