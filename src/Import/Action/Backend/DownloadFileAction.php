<?php

namespace App\Import\Action\Backend;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class DownloadFileAction
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;
    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(FilesystemInterface $filesystem, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->filesystem = $filesystem;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * @Route(
     *     "/storage/download/import/{path}",
     *     name="storage_download_imported_file",
     *     requirements={"path" = ".+"},
     *     methods={"GET"}
     * )
     *
     * @param Request $request
     * @param string  $path
     *
     * @return StreamedResponse
     *
     * @throws FileNotFoundException
     */
    public function __invoke(Request $request, string $path): StreamedResponse
    {
        if (false === $this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            throw new AccessDeniedHttpException();
        }

        $stream = $this->filesystem->readStream($path);

        $pathArray = explode('/', $path);
        end($pathArray);

        $fileName = current($pathArray);

        $response = new StreamedResponse();
        $response->headers->set('Content-Type', $this->filesystem->getMimetype($path));
        $response->headers->set('Content-Length', $this->filesystem->getSize($path));

        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);
        $response->headers->set('Content-Disposition', $dispositionHeader);

        $response->setCallback(
            function () use ($stream) {
                if (0 !== ftell($stream)) {
                    rewind($stream);
                }
                fpassthru($stream);
                fclose($stream);
            }
        );

        $response->send();

        return $response;
    }
}
