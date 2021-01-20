<?php

declare(strict_types=1);

namespace CrudBundle\ArgumentResolver;

use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpFoundation\Request;
use CrudBundle\Action\RedirectResponse;

final class RedirectResponseResolver implements ArgumentValueResolverInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(ContainerInterface $container, RouterInterface $router)
    {
        $this->container = $container;
        $this->router = $router;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if (false === interface_exists($argument->getType())) {
            return false;
        }

        $reflection = new \ReflectionClass($argument->getType());
        if ($reflection->implementsInterface(\CrudBundle\Interfaces\RedirectResponse::class)) {
            return true;
        }

        return false;
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $redirectTo = $this->getClassFromRoute($request->get('_route'), 'redirectTo');
        if (true === $this->container->has($redirectTo)) {
            return $this->container->get($redirectTo);
        }

        $routeParams = $request->get('_route_params');
        yield new RedirectResponse($request, $this->router, $redirectTo, $routeParams);
    }

    private function getClassFromRoute($routeName, $value): ?string
    {
        $collection = $this->router->getRouteCollection();
        $route = $collection->get($routeName);

        return $route->getRequirement($value);
    }
}
