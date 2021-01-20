<?php

namespace App\Translations\View\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\TwigFunction;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

final class LocaleSwitcherExtension extends AbstractExtension
{

    /**
     * @var ParameterBagInterface
     */
    protected $parameterBag;

    /**
     * @var Request|null
     */
    protected $request;

    /**
     * LocaleSwitcherExtension constructor.
     *
     * @param ParameterBagInterface $parameterBag
     * @param RequestStack $requestStack
     */
    public function __construct(ParameterBagInterface $parameterBag, RequestStack $requestStack)
    {
        $this->parameterBag = $parameterBag;
        $this->request = $requestStack->getCurrentRequest();
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('locale_switcher', [$this, 'render'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ])
        ];
    }

    /**
     * @param Environment $environment
     * @param string $template
     * @param array $params
     * @return string
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function render(Environment $environment, string $template, array $params = []): string
    {
        if (!$this->request) {
            return '';
        }

        $currentLocale = $params['locale'] ?? $this->request->getLocale();
        $defaultLocale = $this->parameterBag->get('locale');

        $locales = $params['locales'] ?? $this->parameterBag->get('app.locale_switcher');

        $localesKeys = array_diff(array_keys($locales), [$defaultLocale]);

        $explode = explode('/', $this->request->getRequestUri());
        $explodedUri = array_values(array_diff($explode, ['', null]));

        if (isset($explodedUri[0]) && in_array($explodedUri[0], $localesKeys)) {
            unset($explodedUri[0]);
        }

        $urls = [];
        foreach ($locales as $locale => $label) {
            if ($locale !== $defaultLocale) {
                array_unshift($explodedUri, $locale);
            }
            $urls[$locale] = '/' . implode('/', $explodedUri);
        }

        return $environment->render($template, [
            'currentLocale' => $currentLocale,
            'locales' => $locales,
            'urls' => $urls,
            'routeName' => $this->request->get('_route'),
            'routeParams' => $this->request->get('_route_params', []),
        ]);
    }
}
