<?php

namespace CrudBundle\Action;

use CrudBundle\Interfaces\RedirectResponse;
use Domain\Exception\DomainException;
use Domain\Interfaces\Handler;
use Domain\Interfaces\HandlerRequest;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CommandAction
{
    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(
        ValidatorInterface $validator,
        FlashBagInterface $flashBag
    ) {
        $this->flashBag = $flashBag;
        $this->validator = $validator;
    }

    public function __invoke(
        HandlerRequest $handlerRequest,
        Handler $handler,
        RedirectResponse $redirectResponse
    ) {
        $errors = $this->validator->validate($handlerRequest);
        if (count($errors) > 0) {
            $this->flashBag->set('error', 'save_data.error');

            return new Response([
                'data' => $handlerRequest,
                'errors' => $this->formatErrors($errors),
            ]);
        }

        $result = null;
        try {
            $result = $handler->handle($handlerRequest);

            $this->flashBag->set('success', 'save_data.success');
        } catch (DomainException $e) {
            $this->flashBag->set('error', $e->getMessage());

            return new Response([
                'data' => $handlerRequest,
                'errors' => [
                    'exception' => $e->getMessage(),
                ],
            ]);
        }

        $response = new Response([
            'data' => $handlerRequest,
            'result' => $result,
            'errors' => [],
        ]);

        return $redirectResponse->redirect($response);
    }

    private function formatErrors(ConstraintViolationListInterface $errors): array
    {
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
                            $formattedErrors[$k] = [];
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

        return $contentErrors;
    }
}
