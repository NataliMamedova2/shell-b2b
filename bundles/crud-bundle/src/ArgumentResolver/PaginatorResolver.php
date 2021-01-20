<?php

declare(strict_types=1);

namespace CrudBundle\ArgumentResolver;

use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpFoundation\Request;
use Infrastructure\Interfaces\Paginator\Paginator;

final class PaginatorResolver implements ArgumentValueResolverInterface
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
        $class = $this->getPaginatorName($request);
        if (null === $class) {
            return false;
        }

        if (false === $this->container->has($class)) {
            throw new \InvalidArgumentException(sprintf('Service "%s" not exist or private', $class));
        }

        $reflection = new \ReflectionClass($argument->getType());
        if ($reflection->implementsInterface(Paginator::class)) {
            return true;
        }

        return false;
    }

    /**
     * @param Request $request
     *
     * @return string|null
     */
    private function getPaginatorName(Request $request): ?string
    {
        $collection = $this->router->getRouteCollection();
        $route = $collection->get($request->get('_route'));

        return $route->getRequirement('paginator');
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $class = $this->getPaginatorName($request);

        yield $this->container->get($class);
    }
}
