<?php

declare(strict_types=1);

namespace CrudBundle\ArgumentResolver;

use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpFoundation\Request;
use Domain\Interfaces\Handler;

final class DomainHandlerResolver implements ArgumentValueResolverInterface
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
        $class = $this->getClassFromRoute($request->get('_route'), 'handler');
        if (null === $class) {
            return false;
        }

        if (false === interface_exists($argument->getType())) {
            return false;
        }

        $reflection = new \ReflectionClass($argument->getType());
        if ($reflection->implementsInterface(Handler::class)) {
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

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $class = $this->getClassFromRoute($request->get('_route'), 'handler');
        if (false === $this->container->has($class)) {
            throw new \InvalidArgumentException(sprintf('Service "%s" not exist or private', $class));
        }

        yield $this->container->get($class);
    }
}
