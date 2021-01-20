<?php

namespace App\Clients\View\User\Form;

use App\Clients\Domain\User\ValueObject\Role;
use App\Clients\Domain\User\ValueObject\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class UserFilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $roleNames = Role::getNames();
        $statusNames = Status::getNames();
        $builder
            ->add('role', ChoiceType::class, [
                'required' => false,
                'label' => 'label.role',
                'attr' => [
                    'class' => 'selectpicker',
                ],
                'choices' => array_combine($roleNames, $roleNames),
            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'label' => 'label.status',
                'attr' => [
                    'class' => 'selectpicker',
                ],
                'choices' => array_combine($statusNames, $statusNames),
            ])
            ->add('company', TextType::class, [
                'required' => false,
                'label' => 'label.company',
            ])
            ->add('client1cId', TextType::class, [
                'required' => false,
                'label' => 'label.client_1c_id',
            ])
            ->add('email', TextType::class, [
                'required' => false,
                'label' => 'label.email',
            ])
            ->add('fullName', TextType::class, [
                'required' => false,
                'label' => 'label.full_name',
            ])
            ->add('limit', HiddenType::class, [
                'data' => '25',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
