<?php

namespace App\Api\Action\Api\V1\Partners\Documents\DownloadAction;

use App\Clients\Domain\Document\Document;
use App\Clients\Domain\Document\ValueObject\Type;
use App\Security\Partners\MyselfInterface;
use Behat\Transliterator\Transliterator;
use Infrastructure\Interfaces\Repository\Repository;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class DownloadFileAction
{
    /**
     * @var Repository
     */
    private $documentRepository;
    /**
     * @var MyselfInterface
     */
    private $myself;
    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    public function __construct(
        MyselfInterface $myself,
        Repository $documentRepository,
        FilesystemInterface $filesystem
    ) {
        $this->myself = $myself;
        $this->documentRepository = $documentRepository;
        $this->filesystem = $filesystem;
    }

    public function __invoke(string $id): StreamedResponse
    {
        $partner = $this->myself->getPartner();

        $document = $this->documentRepository->find([
            'id_equalTo' => $id,
            'client1CId_equalTo' => $partner->getClient1CId(),
        ]);

        if (!$document instanceof Document) {
            throw new NotFoundHttpException('Document not found');
        }

        $file = $document->getFile();
        $path = $file->getFile();
        if (false === $this->filesystem->has($path)) {
            throw new NotFoundHttpException('File not found');
        }

        $filename = $this->getDownloadFilename($document);

        $response = new StreamedResponse();

        $stream = $this->filesystem->readStream($path);
        $response->setCallback(
            function () use ($stream) {
                if (0 !== ftell($stream)) {
                    rewind($stream);
                }
                fpassthru($stream);
                fclose($stream);
            }
        );

        $response->headers->set('Content-Type', $this->filesystem->getMimetype($path));
        $response->headers->set('Content-Length', $this->filesystem->getSize($path));
        $response->headers->set('Content-Disposition', sprintf('attachment;filename="%s"', $filename));

        return $response->send();
    }

    private function getDownloadFilename(Document $document): string
    {
        $type = new Type($document->getType());
        $namePrefix = $type->getName();

        $file = $document->getFile();

        $nameSuffix = ('' !== $document->getNumber()) ? $document->getNumber() : $file->getName();
        $name = $namePrefix.'-'.Transliterator::transliterate($nameSuffix);

        return sprintf('%s.%s', $name, $file->getExtension());
    }
}
