<?php

namespace FilesUploader\Action;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class ReadFileAction
{
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * ReadFileAction constructor.
     * @param FilesystemInterface $filesystem
     */
    public function __construct(FilesystemInterface $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param Request $request
     * @param $path
     *
     * @return StreamedResponse
     *
     * @throws FileNotFoundException
     */
    public function __invoke(Request $request, $path)
    {
        if (false === $this->filesystem->has($path)) {
            throw new FileNotFoundException($path);
        }

        $stream = $this->filesystem->readStream($path);

        $response = new StreamedResponse();
        $response->headers->set('Content-Type', $this->filesystem->getMimetype($path));
        $response->headers->set('Content-Length', $this->filesystem->getSize($path));

        $response->setPublic();
        $response->setMaxAge(31536000);
        $response->setSharedMaxAge(31536000);
        $response->setExpires(date_create()->modify('+1 years'));

        if ($request) {
            $response->setLastModified(date_create()->setTimestamp($this->filesystem->getTimestamp($path)));
            $response->isNotModified($request);
        }

        $response->setCallback(function () use ($stream) {
            if (0 !== ftell($stream)) {
                rewind($stream);
            }
            fpassthru($stream);
            fclose($stream);
        });

        $response->send();

        return $response;
    }
}
