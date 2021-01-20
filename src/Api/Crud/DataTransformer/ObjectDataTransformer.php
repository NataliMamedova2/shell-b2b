<?php

namespace App\Api\Crud\DataTransformer;

use App\Api\Crud\Interfaces\DataTransformer;
use App\Api\Resource\Model;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

final class ObjectDataTransformer implements DataTransformer
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var object
     */
    private $model;

    public function __construct(Serializer $serializer, object $model)
    {
        $this->serializer = $serializer;
        $this->model = $model;
    }

    /**
     * @param object $object
     *
     * @return object|array
     *
     * @throws ExceptionInterface
     */
    public function transform($object)
    {
        if ($this->model instanceof Model) {
            $model = clone $this->model;
            return $model->prepare($object);
        }

        $modelClass = get_class($this->model);

        $data = $this->serializer->normalize($object);

        $context = [
            AbstractObjectNormalizer::OBJECT_TO_POPULATE => $this->model,
            AbstractObjectNormalizer::DEEP_OBJECT_TO_POPULATE => true,
            AbstractObjectNormalizer::CIRCULAR_REFERENCE_LIMIT => 2,
        ];

        return $this->serializer->denormalize($data, $modelClass, null, $context);
    }
}
