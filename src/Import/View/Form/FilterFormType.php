<?php

namespace App\Import\View\Form;

use App\Import\Domain\Import\File\ValueObject\Status\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('filename', TextType::class, [
                'required' => false,
                'label' => 'label.filename',
            ])
            ->add('extension', TextType::class, [
                'required' => false,
                'label' => 'label.extension',
            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'label' => 'label.status',
                'attr' => [
                    'class' => 'selectpicker',
                ],
                'choices' => array_combine(Status::getNames(), Status::getNames()),
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
