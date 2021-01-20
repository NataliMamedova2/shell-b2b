<?php

namespace CrudBundle\EventListener;

use CrudBundle\View\Config;
use CrudBundle\View\ViewBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

final class KernelViewSubscriber implements EventSubscriberInterface
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var Environment
     */
    private $templating;
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(
        ParameterBagInterface $parameterBag,
        ContainerInterface $container,
        Environment $templating,
        FlashBagInterface $flashBag
    ) {
        $this->parameterBag = $parameterBag;
        $this->container = $container;
        $this->templating = $templating;
        $this->flashBag = $flashBag;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['onKernelView', 50],
            KernelEvents::RESPONSE => ['onKernelRedirectResponse', 50],
        ];
    }

    public function onKernelView(ViewEvent $event)
    {
        $request = $event->getRequest();

        $matchedRouteName = $request->get('_route');

        $options = $this->parameterBag->get('crud.view');

        $controllerResult = $event->getControllerResult();

        if (!isset($options['contents'][$matchedRouteName])) {
            if ($controllerResult instanceof \CrudBundle\Interfaces\Response) {
                throw new \ErrorException(sprintf('Crud view not configured for "%s"', $matchedRouteName));
            }

            return;
        }
        $viewConfig = $options['contents'][$matchedRouteName];

        $config = new Config(array_merge($options['contents'], $options['blocks'] ?? []));
        $viewConfig = $config->applyInheritance($viewConfig);

        $viewBuilder = new ViewBuilder($config, $this->container);

        $data = [];
        if ($controllerResult instanceof \CrudBundle\Interfaces\Response) {
            $data = $controllerResult->toArray();
        }

        $viewComponent = $viewBuilder->build($viewConfig, $data);

        $content = $this->templating->render($viewComponent->getTemplate(), ['view' => $viewComponent]);
        $redirectResponse = new Response($content);

        $event->setResponse($redirectResponse);
    }

    public function onKernelRedirectResponse(ResponseEvent $event)
    {
        $response = $event->getResponse();

        if (!$response instanceof RedirectResponse) {
            return;
        }

        $request = $event->getRequest();
        $matchedRouteName = $request->get('_route');
        $options = $this->parameterBag->get('crud.view');

        if (!isset($options['contents'][$matchedRouteName])) {
            return;
        }

        $viewConfig = $options['contents'][$matchedRouteName];

        if (isset($viewConfig['flash_message'])) {
            $flashMessage = $viewConfig['flash_message'];

            $this->flashBag->set($flashMessage['type'], $flashMessage['message']);
        }
    }
}
