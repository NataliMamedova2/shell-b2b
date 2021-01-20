<?php

namespace Tests\Unit\TwigBundle\Service;

use App\TwigBundle\Service\AmountSpellOut;
use PHPUnit\Framework\TestCase;

final class AmountSpellOutTest extends TestCase
{
    /**
     * @var AmountSpellOut
     */
    private $service;

    protected function setUp(): void
    {
        $this->service = new AmountSpellOut();
    }

    /**
     * @dataProvider validProviderValues
     *
     * @param $number
     * @param $expected
     */
    public function testSpellOutReturnString($number, $expected): void
    {
        $result = $this->service->spellOut($number, 'uk');

        $this->assertEquals($expected, $result);
    }

    public function validProviderValues()
    {
        return [
            '1' => [1, 'один грн. 00 копійок'],
            '12' => [12, 'дванадцять грн. 00 копійок'],
            '1.0' => [1.0, 'один грн. 00 копійок'],
            '20.10' => ['20.10', 'двадцять грн. 10 копійок'],
            '1.09' => [1.09, 'один грн. 09 копійок'],
            '-1.09' => [-1.09, 'мінус один грн. 09 копійок'],
            '-1.90' => [-1.90, 'мінус один грн. 90 копійок'],
            '-1.92' => [-1.92, 'мінус один грн. 92 копійок'],
            '-1.93' => [-1.93, 'мінус один грн. 93 копійок'],
            '1076724.09' => [1076724.09, 'один мільйон сімдесят шість тисяч сімсот двадцять чотири грн. 09 копійок'],
            '1076724.90' => [1076724.90, 'один мільйон сімдесят шість тисяч сімсот двадцять чотири грн. 90 копійок'],
        ];
    }
}
