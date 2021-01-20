<?php

namespace App\Clients\View\Form\Card\LimitType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

final class TotalLimitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('day', NumberType::class, [
                'label' => 'label.day_uah',
                'html5' => true,
            ])
            ->add('week', NumberType::class, [
                'label' => 'label.week_uah',
                'html5' => true,
            ])
            ->add('month', NumberType::class, [
                'label' => 'label.month_uah',
                'html5' => true,
            ])
        ;
    }
}
