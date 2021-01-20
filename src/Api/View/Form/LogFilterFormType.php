<?php

namespace App\Api\View\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LogFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('resource', TextType::class, [
                'required' => false,
                'label' => 'label.resource',
            ])
            ->add('code', ChoiceType::class, [
                'required' => false,
                'label' => 'label.response.code',
                'attr' => [
                    'class' => 'selectpicker',
                ],
                'choices' => [
                    '200 OK' => 200,
                    '400 Bad request' => 400,
                    '401 Unauthorized' => 401,
                    '404 Not found' => 404,
                    '405 Method not allowed' => 405,
                    '500 Server Error' => 500,
                ],
            ])
            ->add('method', ChoiceType::class, [
                'required' => false,
                'label' => 'label.method',
                'attr' => [
                    'class' => 'selectpicker',
                ],
                'choices' => [
                    'POST' => 'post',
                    'GET' => 'get',
                ],
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
