<?php

namespace App\TwigBundle\Service;

final class AmountSpellOut implements SpellOut
{
    public function spellOut(string $number, string $locale = null): string
    {
        $locale = null !== $locale ? $locale : \Locale::getDefault();

        list($whole, $fraction) = $this->numberBreakdown($number);
        $fractionNumber = (string) round($fraction, 2);

        if (strpos($fractionNumber, '.')) {
            $fractionNumber = explode('.', $fractionNumber)[1];

            if (1 === strlen($fractionNumber)) {
                if ($fraction < 0.1) {
                    $fractionNumber = '0'.$fractionNumber;
                } else {
                    $fractionNumber .= '0';
                }
            }
        }
        if ($fractionNumber === "0") {
            $fractionNumber .= '0';
        }

        $formatter = \NumberFormatter::create($locale, \NumberFormatter::SPELLOUT);

        $coinText = $this->plural(['%s копійка', '%s копійки', '%s копійок'], $fraction, $fractionNumber);

        $number = $this->getNumberSign($number).$whole;

        return $formatter->format($number).' грн. '.$coinText;
    }

    private function numberBreakdown($number)
    {
        if ($number < 0) {
            $number *= -1;
        }

        return [
            floor($number),
            ($number - floor($number)),
        ];
    }

    private function plural(array $endings, $number, string $stringNumber = null): string
    {
        $cases = [2, 0, 1, 1, 1, 2];
        $n = $number;
        if (null === $stringNumber) {
            $stringNumber = (string) $number;
        }

        return sprintf($endings[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]], $stringNumber);
    }

    private function getNumberSign($number): string
    {
        return ((int) $number < 0) ? '-' : '';
    }
}
