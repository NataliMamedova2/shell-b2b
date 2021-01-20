<?php

namespace App\Media\View\Form\Type;

use App\Media\Glide\Service\GlideUrlGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CroppedImageFormType extends AbstractType
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    /**
     * @var GlideUrlGenerator
     */
    private $glideUrlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator, GlideUrlGenerator $glideUrlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
        $this->glideUrlGenerator = $glideUrlGenerator;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', CropperType::class, [
                'mapped' => false,
                'label_attr' => ['class' => 'hidden'],
                'label' => false,
                'attr' => [
                    'data-name' => 'file',
                ],
                'required' => false,
                'help' => 'Max. file size: 10M. Allowed types: *.jpeg, *.png',
            ])
            ->add('fileName', HiddenType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-name' => 'fileName',
                ],
            ])
            ->add('path', HiddenType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'data-name' => 'path',
                ],
            ])
            ->add('cropData', CropperDataType::class, [
                'label' => false,
                'label_attr' => ['class' => 'hidden'],
                'required' => false,
                'attr' => [
                    'class' => 'hidden',
                    'data-name' => 'cropper',
                ],
            ])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['cropper_options'] = $options['cropper_options'];
        $view->vars['allow_remove'] = $options['allow_remove'];

        $view->vars['upload_url'] = $this->urlGenerator->generate('api_media_upload_cropped_image');
        $view->vars['crop_image_url'] = $this->urlGenerator->generate('api_get_cropped_image');

        $view->vars['default_file'] = '';
        $view->vars['original_file'] = '';
        $view->vars['cropper_data'] = $options['cropper_data'];

        $media = $form->getData();
        if (!empty($media['path']) && !empty($media['fileName'])) {
            $path = $media['path'].$media['fileName'];
            $view->vars['original_file'] = $this->urlGenerator->generate('storage_read_file', ['path' => $path]);
            $view->vars['default_file'] = $this->urlGenerator->generate('storage_read_file', ['path' => $path]);

            if (!empty($media['cropData'])) {
                $cropData = $media['cropData'];
                $view->vars['cropper_data'] = $cropData;
                $view->vars['default_file'] = $this->glideUrlGenerator->generate($path, $media['cropData']);
            }
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'csrf_protection' => false,
            'cropper_options' => [],
            'cropper_data' => [],
            'allow_remove' => false,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'upload_cropped_image';
    }
}
