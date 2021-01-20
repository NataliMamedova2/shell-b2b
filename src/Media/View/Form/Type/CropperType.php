<?php

namespace App\Media\View\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CropperType extends AbstractType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getParent(): string
    {
        return FileType::class;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['original_file'] = '';
        $view->vars['cropper_data'] = [];

        $media = $form->getParent() ? $form->getParent()->getData() : null;
        if (!empty($media['path']) && !empty($media['fileName'])) {
            $path = $media['path'].$media['fileName'];
            $view->vars['original_file'] = $this->urlGenerator->generate('storage_read_file', ['path' => $path]);
            $view->vars['cropper_data'] = $media['cropData'] ?? [];
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'cropper_image';
    }
}
