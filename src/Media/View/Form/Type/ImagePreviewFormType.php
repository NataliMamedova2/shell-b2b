<?php

namespace App\Media\View\Form\Type;

use App\Media\Glide\Service\GlideUrlGenerator;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ImagePreviewFormType extends AbstractType
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
            ->add('file', FileType::class, [
                'mapped' => false,
                'label_attr' => ['class' => 'hidden'],
                'label' => false,
                'attr' => [
                    'data-name' => 'file',
                ],
                'required' => false,
                'help' => 'Max. file size: 10M. Allowed types: *.jpeg, *.png'
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
        ;
    }
    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

//        $entity = $form->getParent() ? $form->getParent()->getData() : null;
        $view->vars['default_file'] = '';
        $view->vars['upload_url'] = $this->urlGenerator->generate('api_media_upload_icon');

        $media = $form->getData();
        if ($media && $media->getFile()) {
//            $view->vars['original_file'] = $this->urlGenerator->generate('storage_read_file', ['path' => $media->getFile()]);
//            $view->vars['cropper_data'] = $media->getCropData();
            $view->vars['default_file'] = $this->urlGenerator->generate('storage_read_file', ['path' => $media->getFile()]);
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
            'compound' => true,
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'image_preview';
    }
}
