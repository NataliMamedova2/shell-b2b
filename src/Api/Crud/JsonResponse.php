<?php

namespace App\Api\Crud;

use App\Api\Crud\Interfaces\Response as ApiResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationList;

final class JsonResponse implements ApiResponse
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    public function createErrorResponse($errors): Response
    {
        if ($errors instanceof ConstraintViolationList) {
            $contentErrors = [];
            $formattedErrors = [];
            foreach ($errors as $error) {
                $propertyPath = trim($error->getPropertyPath(), '[]');

                if (false === strpbrk((string) $propertyPath, '[')) {
                    $contentErrors[$propertyPath][] = $error->getMessage();
                    continue;
                }
                $formattedPath = str_replace(['[]', '[', ']'], '/', $propertyPath);
                $formattedPath = str_replace(['//'], '/', $formattedPath);
                $explodeStringPath = explode('/', $formattedPath);

                $arrayPropertyPathName = $explodeStringPath[0] ?? $propertyPath;
                $propertyName = end($explodeStringPath);
                if (2 === count($explodeStringPath)) {
                    $contentErrors[$arrayPropertyPathName][$propertyName][] = $error->getMessage();
                    continue;
                }

                $data = (array) $error->getRoot();
                if (isset($data[$arrayPropertyPathName]) && is_array($data[$arrayPropertyPathName])) {
                    $index = (int) $explodeStringPath[1];

                    $indexErrors[$index] = $error->getMessage();
                    $entryErrors[] = $error->getMessage();
                    foreach ($data[$arrayPropertyPathName] as $k => $item) {
                        if (empty($indexErrors[$k])) {
                            if ($k < $index) {
                                $formattedErrors[$k] = null;
                            }
                            continue;
                        }
                        if (isset($formattedErrors[$k]) && isset($formattedErrors[$k][$propertyName]) && in_array($error->getMessage(), $formattedErrors[$k][$propertyName])) {
                            continue;
                        }

                        $formattedErrors[$k][$propertyName][] = $error->getMessage();
                    }

                    $contentErrors[$arrayPropertyPathName] = $formattedErrors;
                }
            }

            $errors = $contentErrors;
        }

        return $this->getResponse(['errors' => $errors], 400);
    }

    private function getResponse($content, $status = 200): Response
    {
        $response = $this->serializer->serialize($content, 'json');

        return new Response($response, $status, ['Content-Type' => 'application/json']);
    }

    public function createSuccessResponse($data): Response
    {
        return $this->getResponse($data);
    }
}
