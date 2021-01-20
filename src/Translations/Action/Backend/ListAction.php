<?php

declare(strict_types=1);

namespace App\Translations\Action\Backend;

use App\Translations\View\Form\FilterFormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment as Templating;
use Translation\Bundle\Catalogue\CatalogueFetcher;
use Translation\Bundle\Catalogue\CatalogueManager;
use Translation\Bundle\Model\CatalogueMessage;
use Translation\Bundle\Service\ConfigurationManager;

final class ListAction
{
    /**
     * @var ConfigurationManager
     */
    private $configManager;

    /**
     * @var CatalogueManager
     */
    private $catalogueManager;

    /**
     * @var CatalogueFetcher
     */
    private $catalogueFetcher;

    /**
     * @var Templating
     */
    private $templating;

    /**
     * @var array
     */
    private $locales;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(
        FormFactoryInterface $formFactory,
        ConfigurationManager $configManager,
        CatalogueManager $catalogueManager,
        CatalogueFetcher $catalogueFetcher,
        Templating $templating,
        array $locales
    ) {
        $this->formFactory = $formFactory;
        $this->configManager = $configManager;
        $this->catalogueManager = $catalogueManager;
        $this->catalogueFetcher = $catalogueFetcher;
        $this->catalogueFetcher = $catalogueFetcher;
        $this->templating = $templating;
        $this->locales = $locales;
    }

    public function __invoke(Request $request, $configName, $locale, $domain)
    {
        $data = [
            'locale' => $locale,
        ];

        $filter = $this->formFactory->create(FilterFormType::class, $data);

        $filter->handleRequest($request);
        if ($filter->isSubmitted() && $filter->isValid()) {
            $data = $filter->getData();
            $locale = $data['locale'] ?? $locale;
        }

        $config = $this->configManager->getConfiguration($configName);

        // Get a catalogue manager and load it with all the catalogues
        $this->catalogueManager->load($this->catalogueFetcher->getCatalogues($config));

        /** @var CatalogueMessage[] $messages */
        $messages = $this->catalogueManager->getMessages($locale, $domain);
        $jsMessages = $this->catalogueManager->getMessages($locale, 'jsonfile');

        $messages = array_merge($messages, $jsMessages);
        usort($messages, static function (CatalogueMessage $a, CatalogueMessage $b) {
            return strcmp($a->getKey(), $b->getKey());
        });

        $messages = $this->applyFilter($messages, $data);

        return new Response(
            $this->templating->render('backend/translations/translations.html.twig', [
                'form' => $filter->createView(),
                'messages' => $messages,
                'locale' => $locale,
            ])
        );
    }

    /**
     * @param array $messages
     * @param array $data
     *
     * @return array
     */
    private function applyFilter(array $messages, array $data)
    {
        if (empty($data) || (empty($data['key']) && empty($data['message']))) {
            return $messages;
        }

        return array_filter($messages, static function (CatalogueMessage $m) use ($data) {
            $resultStatus = false;
            if (isset($data['key']) && !empty($data['key'])) {
                if (false !== mb_stripos($m->getKey(), $data['key'])) {
                    $resultStatus = true;
                } else {
                    return false;
                }
            }
            if (isset($data['message']) && !empty($data['message'])) {
                if (false !== mb_stripos($m->getMessage(), $data['message'])) {
                    $resultStatus = true;
                } else {
                    return false;
                }
            }

            return $resultStatus;
        });
    }
}
