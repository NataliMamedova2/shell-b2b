<?php

namespace CrudBundle\View\ViewModel;

use CrudBundle\Interfaces\FormDataMapper;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class NormalizerFormDataMapper implements FormDataMapper
{
    /**
     * @var NormalizerInterface
     */
    private $normalizer;

    public function __construct(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function prepareDataToForm(object $data)
    {
        return $this->normalizer->normalize($data);
    }
}
