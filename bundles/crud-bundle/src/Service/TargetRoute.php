<?php

namespace CrudBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class TargetRoute implements \CrudBundle\Interfaces\TargetRoute
{
    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var string
     */
    private $keyPattern = '_route.%s.target_path';

    /**
     * TargetRoute constructor.
     */
    public function __construct(SessionInterface $session, RequestStack $request)
    {
        $this->session = $session;
        $this->request = $request->getCurrentRequest();
    }

    /**
     * Save route params to session.
     *
     * @param array $params []
     */
    public function save(array $params = []): void
    {
        if (empty($this->request)) {
            return;
        }
        $route = $this->request->get('_route');
        $params = array_merge($this->request->query->all(), $params);

        $data = [
            'route' => $route,
            'params' => $params,
        ];
        $this->session->set($this->buildKeyName($route), $data);
    }

    public function getRouteName()
    {
        return $this->request->get('_route');
    }

    private function buildKeyName(string $key): string
    {
        return sprintf($this->keyPattern, $key);
    }

    public function has(string $route): bool
    {
        return $this->session->has($this->buildKeyName($route));
    }

    public function get(string $route): array
    {
        return $this->session->get($this->buildKeyName($route), []);
    }
}
