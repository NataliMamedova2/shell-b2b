<?php

namespace App\Security\View\Twig\Extension;

use App\Security\Application\NonceGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NonceGeneratorExtension extends AbstractExtension
{
    /**
     * @var NonceGenerator
     */
    private $nonceGenerator;

    /**
     * NonceGenerator constructor.
     *
     * @param NonceGenerator $nonceGenerator
     */
    public function __construct(NonceGenerator $nonceGenerator)
    {
        $this->nonceGenerator = $nonceGenerator;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('csp_nonce', [$this->nonceGenerator, 'getNonce']),
        ];
    }
}
