<?php

namespace CrudBundle\Action;

use CrudBundle\Interfaces\Response as CrudResponse;
use Symfony\Component\HttpFoundation\RedirectResponse as Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RedirectResponse implements \CrudBundle\Interfaces\RedirectResponse
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var string
     */
    private $routeName;

    /**
     * @var array
     */
    private $routeParams;

    /**
     * @var int
     */
    private $code = 302;

    public function __construct(
        Request $request,
        UrlGeneratorInterface $urlGenerator,
        string $routeName,
        array $routeParams = []
    ) {
        $this->request = $request;
        $this->urlGenerator = $urlGenerator;
        $this->routeName = $routeName;
        $this->routeParams = $routeParams;
    }

    public function redirect(CrudResponse $data): Response
    {
        return new Response($this->getUrl($data), $this->getCode());
    }

    private function getUrl(CrudResponse $data): string
    {
        $redirect = $this->request->get('redirect');
        if (!empty($redirect) && false === is_array($redirect)) {
            return \stristr($redirect, '?', true);
        }

        $routeName = $this->request->query->all()['routeName'] ?? null;
        if (!empty($redirect) && true === is_array($redirect) && null !== $routeName) {
            return $this->urlGenerator->generate($routeName, $redirect);
        }

        $params = $this->routeParams;

        $entity = $data->getResult();
        $params = array_merge($params, $this->extractFromResult($entity, ['id']));

        return $this->urlGenerator->generate($this->routeName, $params);
    }

    private function extractFromResult($object, array $attributes): array
    {
        $reflectionClass = new \ReflectionClass(get_class($object));
        $array = [];
        foreach ($reflectionClass->getProperties() as $property) {
            if (!in_array($property->getName(), $attributes)) {
                continue;
            }
            $property->setAccessible(true);

            $propertyValue = $property->getValue($object);
            if (is_object($propertyValue) && true === method_exists($propertyValue, '__toString')) {
                $propertyValue = (string) $propertyValue;
            }
            $array[$property->getName()] = $propertyValue;
            $property->setAccessible(false);
        }

        return $array;
    }

    private function getCode(): int
    {
        return $this->code;
    }
}
