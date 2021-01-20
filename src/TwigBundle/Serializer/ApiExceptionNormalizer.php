<?php

namespace App\TwigBundle\Serializer;

use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ApiExceptionNormalizer implements NormalizerInterface
{
    /**
     * @var \Symfony\Component\HttpFoundation\Request|null
     */
    private $request;
    /**
     * @var bool
     */
    private $debug;

    public function __construct(
        RequestStack $requestStack,
        bool $debug = false
    ) {
        $this->request = $requestStack->getCurrentRequest();
        $this->debug = $debug;
    }

    public function normalize($exception, string $format = null, array $context = [])
    {
        $code = $exception->getStatusCode();
        $content = [
            'code' => $code,
            'message' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
        ];

        if (true === $this->debug) {
            $content['exception'] = $exception->getTrace();
        }

        return $content;
    }

    public function supportsNormalization($data, string $format = null)
    {
        return $data instanceof FlattenException && (false !== mb_strpos((string) $this->request->getRequestUri(), '/api/') || false !== mb_strpos((string) $this->request->getContentType(), 'json'));
    }
}
