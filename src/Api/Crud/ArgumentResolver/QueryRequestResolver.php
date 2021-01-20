<?php

declare(strict_types=1);

namespace App\Api\Crud\ArgumentResolver;

use App\Api\Crud\DefaultQueryRequest;
use App\Api\Crud\Interfaces\QueryRequest;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Routing\RouterInterface;

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
        if (false === interface_exists($argument->getType())) {
            return false;
        }

        $reflection = new \ReflectionClass($argument->getType());
        if ($reflection->implementsInterface(QueryRequest::class)) {
            return true;
        }

        return false;
    }

    private function getClassFromRoute($routeName, $value): ?string
    {
        $collection = $this->router->getRouteCollection();
        $route = $collection->get($routeName);

        return $route->getRequirement($value);
    }

    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $class = $this->getClassFromRoute($request->get('_route'), 'request');

        if (empty($class)) {
            yield new DefaultQueryRequest($request);

            return;
        }

        if (class_exists($class) && false === $this->container->has($class)) {
            yield new $class($request);

            return;
        }

        if (false === $this->container->has($class)) {
            throw new \InvalidArgumentException(sprintf('Service "%s" not exist or private', $class));
        }

        yield $this->container->get($class);
    }
}
