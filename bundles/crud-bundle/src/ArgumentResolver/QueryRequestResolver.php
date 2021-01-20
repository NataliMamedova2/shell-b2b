<?php

declare(strict_types=1);

namespace CrudBundle\ArgumentResolver;

use CrudBundle\Action\ListAction\QueryRequest;
use CrudBundle\Interfaces\ListQueryRequest;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpFoundation\Request;

final class QueryRequestResolver implements ArgumentValueResolverInterface
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
        if (null === $argument->getType() || false === interface_exists($argument->getType())) {
            return false;
        }

        $reflection = new \ReflectionClass($argument->getType());
        if ($reflection->implementsInterface(ListQueryRequest::class)) {
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
        $class = $this->getClassFromRoute($request->get('_route'), 'request');

        if (null === $class) {
            yield new QueryRequest($request);

            return;
        }

        if (class_exists($class) && false === $this->container->has($class)) {
            yield new $class($request);

            return;
        }

        if (false === $this->container->has($class)) {
            throw new \InvalidArgumentException(sprintf('Request "%s" not exist or private', $class));
        }

        yield $this->container->get($class);
    }

    private function getClassFromRoute($routeName, $value): ?string
    {
        $collection = $this->router->getRouteCollection();
        $route = $collection->get($routeName);

        return $route->getRequirement($value);
    }
}
