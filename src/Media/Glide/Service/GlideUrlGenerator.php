<?php

namespace App\Media\Glide\Service;

use League\Glide\Urls\UrlBuilderFactory;

final class GlideUrlGenerator
{
    /**
     * @var string
     */
    private $signKey;

    /**
     * UrlBuilder constructor.
     *
     * @param string $signKey
     */
    public function __construct(string $signKey)
    {
        $this->signKey = $signKey;
    }

    /**
     * @param string $path
     * @param array  $cropData
     * @param array  $params
     *
     * @return string
     */
    public function generate(string $path, array $cropData = [], array $params = [])
    {
        if (!empty($cropData)) {
            $cropperParams = [
                (int) $cropData['width'] ?? 0,
                (int) $cropData['height'] ?? 0,
                (int) $cropData['x'] ?? 0,
                (int) $cropData['y'] ?? 0,
            ];
            $params = [
                'crop' => implode(',', $cropperParams),
            ] + $params;
        }

        $urlBuilder = UrlBuilderFactory::create('/image/', $this->signKey);

        return $urlBuilder->getUrl($path, $params);
    }
}
