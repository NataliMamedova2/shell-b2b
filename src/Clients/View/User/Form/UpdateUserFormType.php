<?php

namespace App\Clients\View\User\Form;

use App\Clients\Domain\User\ValueObject\Role;
use App\Clients\Domain\User\ValueObject\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class UpdateUserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $passwordRequired = false;

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
                'required' => $passwordRequired,
                'label' => 'label.password',
            ])
            ->add('repeatPassword', PasswordType::class, [
                'required' => $passwordRequired,
                'label' => 'label.password_repeat',
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'label' => 'label.phone',
                'attr' => [
                    'placeholder' => '+380976544433',
                ],
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'label.role',
                'attr' => [
                    'class' => 'selectpicker',
                ],
                'choices' => array_combine(Role::getNames(), Role::getNames()),
            ])
        ;
    }
}
