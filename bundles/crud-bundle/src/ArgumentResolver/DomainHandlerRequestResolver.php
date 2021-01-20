<?php

declare(strict_types=1);

namespace CrudBundle\ArgumentResolver;

use Domain\Interfaces\HandlerRequest;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

final class DomainHandlerRequestResolver implements ArgumentValueResolverInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(ContainerInterface $container, RouterInterface $router, SerializerInterface $serializer)
    {
        $this->container = $container;
        $this->router = $router;
        $this->serializer = $serializer;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        $class = $this->getClassFromRoute($request->get('_route'), 'request');
        if (null === $class) {
            return false;
        }

        if (false === interface_exists($argument->getType())) {
            return false;
        }

        $reflection = new \ReflectionClass($argument->getType());
        if ($reflection->implementsInterface(HandlerRequest::class)) {
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
        $class = $this->getClassFromRoute($request->get('_route'), 'request');

        if (class_exists($class)) {
            $object = new $class();

            $context = [
                AbstractObjectNormalizer::OBJECT_TO_POPULATE => $object,
            ];

            $data = array_merge($request->request->all(), $request->attributes->all());

            return yield $this->serializer->denormalize($data, $class, null, $context);
        }

        if (false === $this->container->has($class)) {
            throw new \InvalidArgumentException(sprintf('Service "%s" not exist or private', $class));
        }

        yield $this->container->get($class);
    }
}
