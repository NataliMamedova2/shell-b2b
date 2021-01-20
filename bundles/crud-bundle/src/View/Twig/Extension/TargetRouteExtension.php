<?php

namespace CrudBundle\View\Twig\Extension;

use CrudBundle\Service\TargetRoute;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TargetRouteExtension extends AbstractExtension
{
    /**
     * @var TargetRoute
     */
    private $targetRoute;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(TargetRoute $targetRoute, UrlGeneratorInterface $urlGenerator)
    {
        $this->targetRoute = $targetRoute;
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('target_url', [$this, 'getUrl']),
        ];
    }

    public function getUrl(string $routeName, string $default = null): ?string
    {
        if (false === $this->targetRoute->has($routeName)) {
            if (null !== $default) {
                return $default;
            }

            return $this->urlGenerator->generate($routeName);
        }

        $target = $this->targetRoute->get($routeName);

        return $this->urlGenerator->generate($target['route'], $target['params']);
    }
}
