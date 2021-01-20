<?php

namespace App\Media\View\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\HiddenType as HiddenType;

class CropperDataType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('x', HiddenType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'dataX',
                ],
            ])
            ->add('y', HiddenType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'dataY',
                ],
            ])
            ->add('width', HiddenType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'dataWidth',
                ],
            ])
            ->add('height', HiddenType::class, [
                'required' => false,
                'label' => false,
                'attr' => [
                    'class' => 'dataHeight',
                ],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'label' => false,
            'data_class' => null,
            'allow_extra_fields' => true,
        ]);
    }
}
