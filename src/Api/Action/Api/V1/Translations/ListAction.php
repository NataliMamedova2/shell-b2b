<?php

namespace App\Api\Action\Api\V1\Translations;

use Swagger\Annotations as SWG;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;

final class ListAction
{
    /**
     * @var TranslatorBagInterface
     */
    private $translator;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(TranslatorBagInterface $translator, SerializerInterface $serializer)
    {
        $this->translator = $translator;
        $this->serializer = $serializer;
    }

    /**
     * @Route(
     *     "/api/v1/translations/{locale}",
     *     name="api_v1_translations",
     *     methods={"GET"},
     *     requirements={"locale" = "%routing.locales%"}
     * )
     *
     * @SWG\Get(
     *     tags={"Translations"},
     *     operationId="translations-list",
     *     summary="Translations list",
     *     description="**[GET]:** /api/v1/translations/{locale}",
     *      @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *          @SWG\Schema(
     *             type="array",
     *             @SWG\Items(
     *                  @SWG\Property(
     *                      property="translation_key",
     *                      type="string",
     *                      example="translated text"
     *                  )
     *             )
     *         )
     *      )
     * )
     *
     * @param string $locale
     *
     * @return Response
     */
    public function __invoke(string $locale): Response
    {
        $domain = 'jsonfile';

        /** @var MessageCatalogueInterface $catalogue */
        $catalogue = $this->translator->getCatalogue($locale);

        $translations = $catalogue->all($domain);

        $response = $this->serializer->serialize($translations, 'json');

        return new Response($response, 200, ['Content-Type' => 'application/json']);
    }
}
