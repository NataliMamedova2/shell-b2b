<?php

namespace App\Clients\View\Form\Card;

use App\Clients\Domain\Card\ValueObject\ServiceSchedule;
use App\Clients\View\Form\Card\LimitType\FuelLimitsType;
use App\Clients\View\Form\Card\LimitType\GoodsLimitsType;
use App\Clients\View\Form\Card\LimitType\ServiceLimitsType;
use App\Clients\View\Form\Card\LimitType\TotalLimitsType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;

final class UpdateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('totalLimits', TotalLimitsType::class, [
                'label' => 'label.total_limits',
            ])
            ->add('startUseTime', TimeType::class, [
                'label' => 'label.start_use_time',
            ])
            ->add('endUseTime', TimeType::class, [
                'label' => 'label.end_use_time',
            ])
            ->add('serviceDays', ChoiceType::class, [
                'label' => 'label.service_days',
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'selectpicker',
                ],
                'choices' => array_combine(ServiceSchedule::getNames(), ServiceSchedule::getNames()),
            ])
            ->add('fuelLimits', CollectionType::class, [
                'label' => 'label.fuel_limits',
                'attr' => [
                    'class' => 'dynamic__collection',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'entry_type' => FuelLimitsType::class,
            ])
            ->add('goodsLimits', CollectionType::class, [
                'label' => 'label.goods_limits',
                'required' => false,
                'attr' => [
                    'class' => 'dynamic__collection',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'entry_type' => GoodsLimitsType::class,
            ])
            ->add('servicesLimits', CollectionType::class, [
                'label' => 'label.service_limits',
                'required' => false,
                'attr' => [
                    'class' => 'dynamic__collection',
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'entry_type' => ServiceLimitsType::class,
            ])
        ;
    }
}
