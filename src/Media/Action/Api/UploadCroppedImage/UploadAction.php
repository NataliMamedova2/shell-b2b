<?php

namespace App\Media\Action\Api\UploadCroppedImage;

use App\Media\Glide\Service\GlideUrlGenerator;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
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

    /**
     * @var GlideUrlGenerator
     */
    private $glideUrlGenerator;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        UploadHandler $uploadHandler,
        Inflector $inflector,
        UrlGeneratorInterface $urlGenerator,
        GlideUrlGenerator $glideUrlGenerator
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->uploadHandler = $uploadHandler;
        $this->inflector = $inflector;
        $this->urlGenerator = $urlGenerator;
        $this->glideUrlGenerator = $glideUrlGenerator;
    }

    /**
     * @Route(
     *     "/admin/api/upload/cropped-image",
     *     name="api_media_upload_cropped_image",
     *     methods={"POST"}
     * )
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @Security(name="Session")
     * @SWG\Post(
     *     summary="Upload&Crop image",
     *     description="/admin/api/upload/cropped-image",
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
     *             ),
     *             @SWG\Property(
     *                 type="object",
     *                 property="cropData",
     *                 @SWG\Property(
     *                      type="string",
     *                      property="x",
     *                      example="100"
     *                  ),
     *                  @SWG\Property(
     *                      type="string",
     *                      property="y",
     *                      example="120"
     *                  ),
     *                  @SWG\Property(
     *                      type="string",
     *                      property="width",
     *                      example="300"
     *                  ),
     *                  @SWG\Property(
     *                      type="string",
     *                      property="height",
     *                      example="300"
     *                  ),
     *             ),
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
     *                 example="9e314fe3ac95fc55f9d8bb8881cbf1d30b852db6_1563173111.jpeg"
     *             ),
     *             @SWG\Property(
     *                 type="string",
     *                 description="Full path to cropped image",
     *                 property="file",
     *                 example="/image/9e/31/9e314fe3ac95fc55f9d8bb8881cbf1d30b852db6_1563173111.jpeg?crop=189%2C189%2C211%2C68&s=13790bc7b255e48e51a9cfd20bed5c47"
     *             ),
     *             @SWG\Property(
     *                 type="string",
     *                 description="Full path to original image",
     *                 property="originalFile",
     *                 example="/storage/a6/ed/a6edcc5a6693ffe6eda0dfa017b17531b46c3b8c_1563173143.jpeg"
     *             ),
     *             @SWG\Property(
     *                 type="string",
     *                 property="extension",
     *                 example="jpeg"
     *             ),
     *             @SWG\Property(
     *                 type="object",
     *                 property="cropData",
     *                 @SWG\Property(
     *                      type="string",
     *                      property="x",
     *                      example="100"
     *                  ),
     *                  @SWG\Property(
     *                      type="string",
     *                      property="y",
     *                      example="120"
     *                  ),
     *                  @SWG\Property(
     *                      type="string",
     *                      property="width",
     *                      example="300"
     *                  ),
     *                  @SWG\Property(
     *                      type="string",
     *                      property="height",
     *                      example="300"
     *                  ),
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
        $result['originalFile'] = $this->urlGenerator->generate('storage_read_file', ['path' => $file]);

        $cropData = $inputModel->getCropData();
        $result['file'] = $this->glideUrlGenerator->generate($file, $cropData);
        $result['cropData'] = $cropData;

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
