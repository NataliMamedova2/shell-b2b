<?php

declare(strict_types=1);

namespace App\Api\Action\Backend;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class GenerateDocAction
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @Route(
     *     "/admin/api/doc/generate",
     *     name="admin_api_doc_generate",
     *     methods={"GET"}
     * )
     *
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request): Response
    {
        $area = 'default';
        if (!$this->container->has($area)) {
            throw new BadRequestHttpException(sprintf('Area "%s" is not supported.', $area));
        }

        $spec = $this->container->get($area)->generate()->toArray();
        $spec['host'] = $request->getHost();

        $spec['schemes'] = [$request->getScheme()];

        array_walk($spec['paths'], function (&$v, $k) {
            if (isset($v['options'])) {
                unset($v['options']);
            }
        });

        return new JsonResponse($spec);
    }
}
