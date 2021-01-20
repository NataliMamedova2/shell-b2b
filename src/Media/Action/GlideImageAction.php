<?php

namespace App\Media\Action;

use League\Flysystem\FilesystemInterface;
use League\Glide\Responses\SymfonyResponseFactory;
use League\Glide\Server;
use League\Glide\Signatures\SignatureException;
use League\Glide\Signatures\SignatureFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class GlideImageAction
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var Server
     */
    private $glideServer;
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * GlideImageAction constructor.
     *
     * @param FilesystemInterface   $filesystem
     * @param ParameterBagInterface $parameterBag
     * @param Server                $glideServer
     */
    public function __construct(
        FilesystemInterface $filesystem,
        ParameterBagInterface $parameterBag,
        Server $glideServer
    ) {
        $this->parameterBag = $parameterBag;
        $this->glideServer = $glideServer;
        $this->filesystem = $filesystem;
    }

    /**
     * @Route(
     *     "/image/{path}",
     *     name="glide_image",
     *     methods={"GET"},
     *     requirements={
     *      "path"=".+"
     *     }
     * )
     *
     * @param Request $request
     * @param string  $path
     *
     * @return Response
     */
    public function __invoke(Request $request, string $path)
    {
        if (false === $this->filesystem->has($path)) {
            throw new NotFoundHttpException(sprintf('File not found at path: %s', $path));
        }

        $params = $request->query->all();

        try {
            $signKey = $this->parameterBag->get('kernel.secret');
            SignatureFactory::create($signKey)->validateRequest('/image/'.$path, $params);
        } catch (SignatureException $e) {
            throw new NotFoundHttpException();
        }

        $this->glideServer->setResponseFactory(new SymfonyResponseFactory($request));

        return $this->glideServer->getImageResponse($path, $params);
    }
}
