<?php

namespace App\Clients\View\Form\Company;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class SignUpFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'label.username',
            ])
            ->add('email', EmailType::class, [
                'label' => 'label.email',
            ])
            ->add('lastName', TextType::class, [
                'label' => 'label.last_name',
            ])
            ->add('firstName', TextType::class, [
                'label' => 'label.first_name',
            ])
            ->add('middleName', TextType::class, [
                'required' => false,
                'label' => 'label.middle_name',
            ])
            ->add('password', PasswordType::class, [
                'required' => true,
                'label' => 'label.password',
            ])
            ->add('repeatPassword', PasswordType::class, [
                'required' => true,
                'label' => 'label.password_repeat',
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'label' => 'label.phone',
                'help' => '+80976544433',
                'attr' => [
                    'placeholder' => '+80976544433',
                ],
            ])
            ;
    }
}
