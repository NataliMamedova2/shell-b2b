<?php

namespace App\Clients\View\Form\Client;

use App\Clients\Domain\Client\ValueObject\Status;
use App\Clients\Domain\Client\ValueObject\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FilterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('clientId', TextType::class, [
                'required' => false,
                'label' => 'label.client_1c_id',
            ])
            ->add('clientName', TextType::class, [
                'required' => false,
                'label' => 'label.company_name',
            ])
            ->add('managerId', TextType::class, [
                'required' => false,
                'label' => 'label.manager_1c_id',
            ])
            ->add('cardNumber', TextType::class, [
                'required' => false,
                'label' => 'label.card_number',
            ])
            ->add('type', ChoiceType::class, [
                'required' => false,
                'label' => 'label.type',
                'choices' => array_flip(Type::getNames()),
            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'label' => 'label.status',
                'choices' => array_flip(Status::getNames()),
            ])
            ->add('registerStatus', ChoiceType::class, [
                'required' => false,
                'label' => 'label.register_status',
                'choices' => [
                    'registered' => 'registered',
                    'not-registered' => 'not-registered',
                    'resend-register-link' => 'resend-register-link',
                ],
            ])
            ->add('limit', HiddenType::class, [
                'data' => '25',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
