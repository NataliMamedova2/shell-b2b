<?php

namespace App\Api\Application\Subscriber;

use App\Api\Domain\Log\UseCase\Create\Handler;
use App\Api\Domain\Log\UseCase\Create\HandlerRequest;
use App\Api\Domain\Log\ValueObject\IPAddress;
use App\Api\Domain\Log\ValueObject\Request;
use App\Api\Domain\Log\ValueObject\Resource;
use App\Api\Domain\Log\ValueObject\Response as ApiResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class KernelResponseSubscriber implements EventSubscriberInterface
{
    /**
     * @var Handler
     */
    private $createLogHandler;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @var \Throwable|null
     */
    private $exception = null;

    private $ignoreResourceLogging = [
        '/api/v1/last-system-update',
    ];

    /**
     * KernelResponseSubscriber constructor.
     *
     * @param Handler         $createLogHandler
     * @param LoggerInterface $logger
     * @param bool            $debug
     */
    public function __construct(
        Handler $createLogHandler,
        LoggerInterface $logger,
        bool $debug = false
    ) {
        $this->createLogHandler = $createLogHandler;
        $this->logger = $logger;
        $this->debug = $debug;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException'],
            KernelEvents::RESPONSE => ['onKernelResponse'],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $this->exception = $event->getThrowable();
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();

        if (0 !== strpos($request->getPathInfo(), '/api/')) {
            return;
        }

        if (true === in_array($request->getPathInfo(), $this->ignoreResourceLogging)) {
            return;
        }
        $response = $event->getResponse();

        if ($this->exception instanceof \Throwable && 200 === $response->getStatusCode()) {
            return;
        }

        $responseHeaders = $this->formatHeaders($response->headers->all());
        $responseContent = json_decode($response->getContent(), true);

        if ($this->exception instanceof \Throwable) {
            $responseContent = [
                'code' => $response->getStatusCode(),
                'message' => $this->exception->getMessage(),
                'exception' => $this->exception->getTrace(),
            ];

            $code = $response->getStatusCode();
            $content = [
                'code' => $code,
                'message' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
            ];

            if (true === $this->debug) {
                $content['exception'] = $this->exception->getTrace();
            }
            $response->headers->set('Content-Type', 'application/json');
            $response->setContent(json_encode($responseContent));
        }

        try {
            $headers = $this->formatHeaders($request->headers->all());

            $requestContent = $request->getContent() ? json_decode($request->getContent(), true) : [];

            array_walk($requestContent, function (&$v, $k) {
                if ('password' === $k) {
                    $v = '***';
                }
            });

            $handlerRequest = new HandlerRequest();
            $handlerRequest->resource = new Resource($request->getRequestUri());
            $handlerRequest->request = new Request($request->getMethod(), $headers, $requestContent);
            $handlerRequest->response = new ApiResponse($response->getStatusCode(), $responseHeaders, $responseContent);
            $handlerRequest->IPAddress = new IPAddress($request->getClientIp() ?? '127.0.0.1');

            $this->createLogHandler->handle($handlerRequest);
        } catch (\Exception $e) {
            $this->logger->error($e->getMessage(), $e->getTrace());
        }
    }

    private function formatHeaders(array $headers): array
    {
        if (empty($headers)) {
            return [];
        }

        ksort($headers);
        $content = [];
        foreach ($headers as $name => $values) {
            $name = ucwords($name, '-');
            foreach ($values as $value) {
                $content[$name] = $value;
            }
        }

        return $content;
    }
}
