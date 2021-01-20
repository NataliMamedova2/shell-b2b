<?php

namespace App\Media\View\Twig\Extension;

use App\Media\Glide\Service\GlideUrlGenerator;
use App\Media\Model\CropperInterface;
use App\Media\Model\MediaInterface;
use League\Flysystem\FilesystemInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class MediaExtension extends AbstractExtension
{
    /**
     * @var GlideUrlGenerator
     */
    private $glideUrlGenerator;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * MediaExtension constructor.
     *
     * @param GlideUrlGenerator   $glideUrlGenerator
     * @param FilesystemInterface $filesystem
     */
    public function __construct(GlideUrlGenerator $glideUrlGenerator, FilesystemInterface $filesystem)
    {
        $this->glideUrlGenerator = $glideUrlGenerator;
        $this->filesystem = $filesystem;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('media_image', [$this, 'mediaImage']),
        ];
    }

    /**
     * @param MediaInterface|null $media
     * @param int $width
     * @param int $height
     *
     * @return string
     */
    public function mediaImage(?MediaInterface $media, $width = 0, $height = 0): string
    {
        if (null === $media || false === $this->filesystem->has($media->getFile())) {
            return '';
        }

        $cropData = [];
        if ($media instanceof CropperInterface) {
            $cropData = $media->getCropData();
        }
        $params = [
            'w' => $width,
            'h' => $height,
        ];

        return $this->glideUrlGenerator->generate($media->getFile(), $cropData, array_filter($params));
    }
}
