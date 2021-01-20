<?php

namespace App\Clients\View\RecoverPass;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class RecoverPassFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', PasswordType::class, [
                'required' => true,
                'label' => 'label.password',
            ])
            ->add('repeatPassword', PasswordType::class, [
                'required' => true,
                'label' => 'label.password_repeat',
            ])
            ;
    }
}
