<?php

namespace App\Users\View\Form;

use App\Media\View\Form\Type\CroppedImageFormType;
use App\Users\Domain\User\ValueObject\Role;
use App\Users\Domain\User\ValueObject\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('manager1CId', TextType::class, [
                'required' => false,
                'label' => 'label.manager_1c_id',
            ])
            ->add('username', TextType::class, [
                'label' => 'label.username',
            ])
            ->add('email', EmailType::class, [
                'label' => 'label.email',
            ])
            ->add('name', TextType::class, [
                'label' => 'label.full_name',
            ])
            ->add('password', PasswordType::class, [
                'required' => false,
                'label' => 'label.password',
            ])
            ->add('repeatPassword', PasswordType::class, [
                'required' => false,
                'label' => 'label.password_repeat',
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'label' => 'label.phone',
                'attr' => [
                    'placeholder' => '+380976544433',
                ],
            ])
            ->add('avatar', CroppedImageFormType::class, [
                'label' => false,
                'cropper_options' => [
                    'aspectRatio' => 1,
                ],
                'help' => 'Max. file size: 10M. Allowed types: *.jpeg, *.png',
            ])
            ->add('role', ChoiceType::class, [
                'label' => 'label.role',
                'attr' => [
                    'class' => 'selectpicker',
                ],
                'choices' => array_flip(Role::getNames()),
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'label.status',
                'attr' => [
                    'class' => 'selectpicker',
                ],
                'choices' => array_flip(Status::getNames()),
            ])
        ;
    }
}
