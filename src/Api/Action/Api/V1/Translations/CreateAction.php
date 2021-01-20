<?php

namespace App\Api\Action\Api\V1\Translations;

use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Translation\Bundle\Service\CacheClearer;
use Translation\Bundle\Service\StorageManager;
use Translation\Bundle\Service\StorageService;
use Translation\Common\Exception\StorageException;
use Translation\Common\Model\Message;
use Translation\Common\Model\MessageInterface;

final class CreateAction
{
    /**
     * @var StorageManager
     */
    private $storageManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var CacheClearer
     */
    private $cacheClearer;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(
        StorageManager $storageManager,
        ValidatorInterface $validator,
        CacheClearer $cacheClearer,
        SerializerInterface $serializer
    ) {
        $this->storageManager = $storageManager;
        $this->validator = $validator;
        $this->cacheClearer = $cacheClearer;
        $this->serializer = $serializer;
    }

    /**
     * @Route(
     *     "/api/v1/translations/create/{locale}",
     *     name="api_v1_translations_create",
     *     methods={"POST", "OPTIONS"},
     *     requirements={"locale" = "%routing.locales%"}
     * )
     *
     * @SWG\Post(
     *     tags={"Translations"},
     *     operationId="translations-create",
     *     summary="Create translation",
     *     description="**[POST]:** /api/v1/translations/create/{locale}",
     *      @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="JSON Payload",
     *         required=true,
     *         @SWG\Schema(
     *             type="object",
     *             required={"key"},
     *             @SWG\Property(type="string", property="key", example="translation_key"),
     *             @SWG\Property(type="string", property="translation", example="translation text"),
     *         )
     *     )
     * )
     * @SWG\Response(
     *     response=200,
     *     description="successful operation",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Property(
     *             property="success",
     *             type="bool",
     *             example="true"
     *         )
     *     )
     * )
     * @SWG\Response(
     *     response=400,
     *     description="bad request",
     *     @SWG\Schema(ref="#/definitions/ValidationErrors")
     * )
     */
    public function __invoke(Request $request, string $locale): Response
    {
        $domain = 'frontend';
        $configName = 'frontend';

        /** @var StorageService $storage */
        $storage = $this->storageManager->getStorage($configName);

        $message = $this->getMessageFromRequest($request);
        $message = $message->withDomain($domain);
        $message = $message->withLocale($locale);

        $errors = $this->validator->validate($message, null, ['Create']);
        if (count($errors) > 0) {
            $errorList = [];
            foreach ($errors as $error) {
                $errorList[$error->getPropertyPath()] = [$error->getMessage()];
            }

            return $this->getResponse($errorList, 400);
        }

        try {
            $storage->create($message);
        } catch (StorageException $e) {
            throw new BadRequestHttpException(sprintf('Key "%s" does already exist for "%s" on domain "%s".', $message->getKey(), $locale, $domain), $e);
        }

//        $this->cacheClearer->clearAndWarmUp($locale);

        $data = [
            'success' => true,
        ];

        return $this->getResponse($data, 200);
    }

    private function getMessageFromRequest(Request $request): MessageInterface
    {
        $json = $request->getContent();
        $data = json_decode($json, true);

        $key = $data['key'] ?? null;
        $translation = $data['translation'] ?? null;

        return new Message($key, '', '', $translation);
    }

    /**
     * @param array|object $content
     * @param int          $status
     *
     * @return Response
     */
    private function getResponse($content, $status)
    {
        $response = $this->serializer->serialize($content, 'json');

        return new Response($response, $status, ['Content-Type' => 'application/json']);
    }
}
