<?php

namespace App\Media\Action\Api\UploadIcon;

use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Common\Inflector\Inflector;
use FilesUploader\Handler\UploadHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Swagger\Annotations as SWG;

final class UploadAction
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var UploadHandler
     */
    private $uploadHandler;

    /**
     * @var Inflector
     */
    private $inflector;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UploadHandler $uploadHandler,
        Inflector $inflector,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->uploadHandler = $uploadHandler;
        $this->inflector = $inflector;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @Route(
     *     "/admin/api/v1/upload/icon",
     *     name="api_media_upload_icon",
     *     methods={"POST"}
     * )
     *
     * @Security(name="Session")
     * @SWG\Post(
     *     summary="Upload icon",
     *     description="/admin/api/upload/icon",
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     tags={"Media"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 type="string",
     *                 property="file",
     *                 example="data:image/jpeg;base64......."
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful response",
     *          @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 type="string",
     *                 property="path",
     *                 example="9e/31/"
     *             ),
     *             @SWG\Property(
     *                 type="string",
     *                 property="fileName",
     *                 example="9e314fe3ac95fc55f9d8bb8881cbf1d30b852db6_1563173111.png"
     *             ),
     *             @SWG\Property(
     *                 type="string",
     *                 description="Full path to icon",
     *                 property="file",
     *                 example="/storage/9e/31/9e314fe3ac95fc55f9d8bb8881cbf1d30b852db6_1563173111.png"
     *             ),
     *             @SWG\Property(
     *                 type="string",
     *                 property="extension",
     *                 example="png"
     *             ),
     *         )
     *     )
     * )
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function __invoke(Request $request)
    {
        $inputModel = InputModel::createFromRequest($request);

        $file = $inputModel->getFile();

        $errors = $this->validator->validate($inputModel);
        if (count($errors)) {
            return $this->createFailureResponse($errors);
        }

        $result = $this->uploadHandler->handle($file);

        $file = $result['path'].$result['fileName'];
        $result['file'] = $this->urlGenerator->generate('storage_read_file', ['path' => $file]);

        return $this->getResponse($result);
    }

    /**
     * @param array|ConstraintViolationListInterface $content
     * @param int                                    $status
     *
     * @return Response
     */
    private function createFailureResponse($content, $status = 400)
    {
        $errorList = null;

        if ($content instanceof ConstraintViolationListInterface) {
            foreach ($content as $error) {
                $error = $this->getErrorFromValidation($error);
                $errorList[$error['key']] = $error['value'];
            }
        } else {
            $errorList = $content;
        }

        return $this->getResponse(['errors' => $errorList], $status);
    }

    /**
     * @param ConstraintViolationInterface $error
     *
     * @return array
     */
    private function getErrorFromValidation($error)
    {
        $properties = $this->inflector->tableize($error->getPropertyPath());

        return ['key' => $properties, 'value' => $error->getMessage()];
    }

    /**
     * @param array|object $content
     * @param int          $status
     *
     * @return Response
     */
    private function getResponse($content, $status = 200)
    {
        $response = $this->serializer->serialize($content, 'json');

        return new Response($response, $status, ['Content-Type' => 'application/json']);
    }
}
