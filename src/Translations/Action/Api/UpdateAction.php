<?php

declare(strict_types=1);

namespace App\Translations\Action\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Translation\Bundle\Exception\MessageValidationException;
use Translation\Bundle\Service\CacheClearer;
use Translation\Bundle\Service\StorageManager;
use Translation\Bundle\Service\StorageService;
use Translation\Common\Model\Message;
use Translation\Common\Model\MessageInterface;

final class UpdateAction
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
     * UpdateAction constructor.
     */
    public function __construct(
        StorageManager $storageManager,
        ValidatorInterface $validator,
        CacheClearer $cacheClearer
    ) {
        $this->storageManager = $storageManager;
        $this->validator = $validator;
        $this->cacheClearer = $cacheClearer;
    }

    /**
     * @param string $configName
     * @param string $locale
     * @param string $domain
     *
     * @return Response
     */
    public function __invoke(Request $request, $configName, $locale, $domain)
    {
        try {
            $message = $this->getMessageFromRequest($request);
            $message = $message->withDomain($domain);
            $message = $message->withLocale($locale);
            $message = $message->withAddedMeta('new', false);

            $this->validateMessage($message, ['Edit']);
        } catch (MessageValidationException $e) {
            return new JsonResponse([
                'message' => $e->getMessage(),
            ], 400);
        }

        /** @var StorageService $storage */
        $storage = $this->storageManager->getStorage($configName);
        $storage->update($message);

        $newMessage = $storage->syncAndFetchMessage($locale, $domain, $message->getKey());

        sleep(1);
        $this->cacheClearer->clearAndWarmUp($locale);

        return new JsonResponse([
            'key' => $newMessage->getKey(),
            'message' => $newMessage->getTranslation(),
        ]);
    }

    private function getMessageFromRequest(Request $request): MessageInterface
    {
        $json = $request->getContent();
        $data = json_decode($json, true);
        $message = new Message($data['key']);
        if (isset($data['message'])) {
            $message = $message->withTranslation($data['message']);
        }

        return $message;
    }

    /**
     * @throws MessageValidationException
     */
    private function validateMessage(MessageInterface $message, array $validationGroups): void
    {
        $errors = $this->validator->validate($message, null, $validationGroups);
        if (count($errors) > 0) {
            throw MessageValidationException::create();
        }
    }
}
