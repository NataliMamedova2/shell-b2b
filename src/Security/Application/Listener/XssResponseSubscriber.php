<?php

namespace App\Security\Application\Listener;

use App\Security\Application\NonceGenerator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Exception;

final class XssResponseSubscriber implements EventSubscriberInterface
{
    /**
     * @var NonceGenerator
     */
    private $nonceGenerator;

    /**
     * @var string
     */
    private $environment;

    /**
     * @param NonceGenerator $nonceGenerator
     * @param string         $environment
     */
    public function __construct(NonceGenerator $nonceGenerator, string $environment)
    {
        $this->nonceGenerator = $nonceGenerator;
        $this->environment = $environment;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'xssProtector',
        ];
    }

    /**
     * @param ResponseEvent $event
     *
     * @throws Exception
     */
    public function xssProtector(ResponseEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request instanceof Request || strpos($request->getPathInfo(), '/admin') === 0) {
            return;
        }

        $response = $event->getResponse();

        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('X-Frame-Options', 'sameorigin');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Feature-Policy', 'camera \'none\'; microphone \'none\'');

        if ($this->environment === 'prod') {
            $host = $request->getHost();
            $nonce = $this->nonceGenerator->getNonce();
            $cspPolicyHeader = "script-src 'nonce-".$nonce."' 'unsafe-inline' 'strict-dynamic' https: http:; object-src 'none'; frame-ancestors *.$host;";

            // set CPS header on the response object
            $response->headers->set('Content-Security-Policy', $cspPolicyHeader);
            $response->headers->set('X-Content-Security-Policy', $cspPolicyHeader);
            $response->headers->set('X-WebKit-CSP', $cspPolicyHeader);
        }
    }
}
