<?php

namespace App\Application\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Jenssegers\Agent\Agent;

final class OutdatedBrowserSubscriber implements EventSubscriberInterface
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => ['onKernelResponse'],
        ];
    }

    /**
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event): void
    {
        $request = $event->getRequest();

        /**
         * @var Agent
         */
        $agent = new Agent();

        if (true === $agent->isRobot() || 0 === strpos($request->getPathInfo(), '/admin') || 0 === strpos($request->getPathInfo(), '/api')) {
            return;
        }

        $routeName = $request->get('_route');

        $isOutdatedBrowser = $this->isOutdatedBrowser($agent);
        if (false === $isOutdatedBrowser && 'frontend_outdated_browser' !== $routeName) {
            return;
        }

        if (true === $isOutdatedBrowser && 'frontend_outdated_browser' === $routeName) {
            return;
        }

        $redirectUrl = $this->urlGenerator->generate('frontend_outdated_browser');
        if (false === $isOutdatedBrowser && 'frontend_outdated_browser' === $routeName) {
            $redirectUrl = $this->urlGenerator->generate('frontend_homepage');
        }

        $redirectResponse = new RedirectResponse($redirectUrl);
        $event->setResponse($redirectResponse);
    }

    /**
     * @param Agent $agent
     *
     * @return bool
     */
    private function isOutdatedBrowser(Agent $agent): bool
    {
        $browserName = $agent->browser();
        $browserVersion = (string) $agent->version($browserName);

        $isOutdatedBrowser = false;
        if (true === $agent->isDesktop()) {
            if ('IE' === $browserName && version_compare($browserVersion, 11, '<')) {
                $isOutdatedBrowser = true;
            }
            if ('Edge' === $browserName && version_compare($browserVersion, 17, '<')) {
                $isOutdatedBrowser = true;
            }
            if ('Safari' === $browserName && version_compare($browserVersion, 12, '<')) {
                $isOutdatedBrowser = true;
            }
            if ('Opera' === $browserName && version_compare($browserVersion, 57, '<')) {
                $isOutdatedBrowser = true;
            }
            if ('Chrome' === $browserName && version_compare($browserVersion, 71, '<')) {
                $isOutdatedBrowser = true;
            }
            if ('Firefox' === $browserName && version_compare($browserVersion, 65, '<')) {
                $isOutdatedBrowser = true;
            }
        }

        if (true === $agent->isMobile()) {
            if ('Safari' === $browserName && version_compare($browserVersion, 10, '<')) {
                $isOutdatedBrowser = true;
            }
            if ('Chrome' === $browserName && version_compare($browserVersion, 49, '<')) {
                $isOutdatedBrowser = true;
            }
            if ('Opera' === $browserName && version_compare($browserVersion, 46, '<')) {
                $isOutdatedBrowser = true;
            }
        }

        return $isOutdatedBrowser;
    }
}
