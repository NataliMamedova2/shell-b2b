<?php

declare(strict_types=1);

namespace CrudBundle\ArgumentResolver;

use CrudBundle\Interfaces\ReadQueryRequest;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpFoundation\Request;

final class ReadQueryRequestResolver implements ArgumentValueResolverInterface
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
        if ($reflection->implementsInterface(ReadQueryRequest::class)) {
            return true;
        }

        return false;
    }

    /**
     * @param Request $request
     *
     * @return string|null
     */
    private function getQueryRequestName(Request $request): ?string
    {
        $collection = $this->router->getRouteCollection();
        $route = $collection->get($request->get('_route'));

        return $route->getRequirement('request');
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $class = $this->getQueryRequestName($request);

        $queryRequest = new \CrudBundle\ReadQueryRequest($request);
        if ($this->container->has($class)) {
            $queryRequest = $this->container->get($class);
        }

        yield $queryRequest;
    }
}
