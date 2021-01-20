<?php

namespace App\Media\Action\Api;

use App\Media\Glide\Service\GlideUrlGenerator;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swagger\Annotations as SWG;

final class CropImageAction
{

    /**
     * @var GlideUrlGenerator
     */
    private $glideUrlGenerator;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * GlideImageAction constructor.
     * @param SerializerInterface $serializer
     * @param GlideUrlGenerator $glideUrlGenerator
     */
    public function __construct(
        SerializerInterface $serializer,
        GlideUrlGenerator $glideUrlGenerator
    ) {
        $this->glideUrlGenerator = $glideUrlGenerator;
        $this->serializer = $serializer;
    }

    /**
     * @Route(
     *     "/admin/api/crop-image",
     *     name="api_get_cropped_image",
     *     methods={"POST"}
     * )
     *
     * @IsGranted("ROLE_ADMIN")
     *
     * @Security(name="Session")
     * @SWG\Post(
     *     summary="Crop image",
     *     description="/admin/api/crop-image",
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
     *                 property="path",
     *                 example="9e/31/"
     *             ),
     *             @SWG\Property(
     *                 type="string",
     *                 property="fileName",
     *                 example="9e314fe3ac95fc55f9d8bb8881cbf1d30b852db6_1563173111.jpeg"
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
     * @param Request $request
     *
     * @return Response
     */
    public function __invoke(Request $request)
    {
        $inputData = json_decode($request->getContent(), true);

        $file = $inputData['path'] . $inputData['fileName'];

        $cropData = $inputData['cropData'];
        $result = [
            'path' => $inputData['path'],
            'fileName' => $inputData['fileName'],
            'file' => $this->glideUrlGenerator->generate($file, $cropData),
            'cropData' => $cropData,
        ];

        $response = $this->serializer->serialize($result, 'json');

        return new Response($response, 200, ['Content-Type' => 'application/json']);
    }
}
