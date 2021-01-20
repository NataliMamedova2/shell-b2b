<?php

namespace App\Clients\View\Form\Card;

use App\Clients\Domain\Card\ValueObject\CardStatus;
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
            ->add('cardNumber', TextType::class, [
                'required' => false,
                'label' => 'label.card_number',
            ])
            ->add('status', ChoiceType::class, [
                'required' => false,
                'label' => 'label.status',
                'choices' => array_flip(CardStatus::getNames()),
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
