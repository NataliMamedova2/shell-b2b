<?php

declare(strict_types=1);

namespace CrudBundle\ArgumentResolver;

use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpFoundation\Request;

final class RepositoryResolver implements ArgumentValueResolverInterface
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
        $class = $this->getRepositoryName($request);
        if (null === $class) {
            return false;
        }

        if (false === $this->container->has($class)) {
            throw new \InvalidArgumentException(sprintf('Repository "%s" not exist or private', $class));
        }

        $reflection = new \ReflectionClass($argument->getType());
        if ($reflection->implementsInterface(Repository::class)) {
            return true;
        }

        return false;
    }

    /**
     * @param Request $request
     *
     * @return string|null
     */
    private function getRepositoryName(Request $request): ?string
    {
        $collection = $this->router->getRouteCollection();
        $route = $collection->get($request->get('_route'));

        return $route->getRequirement('repository');
    }

    /**
     * @param Request          $request
     * @param ArgumentMetadata $argument
     *
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        $class = $this->getRepositoryName($request);

        yield $this->container->get($class);
    }
}
