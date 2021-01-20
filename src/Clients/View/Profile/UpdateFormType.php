<?php

namespace App\Clients\View\Profile;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class UpdateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'label.company_name',
            ])
            ->add('email', TextType::class, [
                'label' => 'label.email',
            ])
            ->add('postalAddress', TextType::class, [
                'required' => false,
                'label' => 'label.postal_address',
            ])
            ->add('accountingEmail', EmailType::class, [
                'required' => false,
                'label' => 'label.accounting_email',
            ])
            ->add('accountingPhone', TextType::class, [
                'required' => false,
                'label' => 'label.accounting_phone',
            ])
        ;
    }
}
