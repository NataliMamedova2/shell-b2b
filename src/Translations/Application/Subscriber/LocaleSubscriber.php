<?php

declare(strict_types=1);

namespace App\Translations\Application\Subscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class LocaleSubscriber implements EventSubscriberInterface
{
    /**
     * @var array
     */
    private $locales;

    /**
     * LocaleSubscriber constructor.
     *
     * @param array $locales
     */
    public function __construct(array $locales)
    {
        $this->locales = $locales;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::REQUEST => [['onKernelRequest', 20]],
            KernelEvents::EXCEPTION => [['onKernelException', 200]],
        ];
    }

    /**
     * @param RequestEvent $event
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($locale = $request->get('_locale')) {
            $request->setLocale($locale);
        }
    }

    /**
     * @param ExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->get('_locale')) {
            return;
        }

        $pathInfoArray = explode('/', $request->getPathInfo());
        $pathInfoArray = array_values(array_diff($pathInfoArray, ['']));

        if (!isset($pathInfoArray[0]) || !in_array($pathInfoArray[0], $this->locales, true)) {
            return;
        }

        $request->setLocale($pathInfoArray[0]);
    }
}
