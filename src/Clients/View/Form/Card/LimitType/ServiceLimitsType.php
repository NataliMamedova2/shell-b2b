<?php

namespace App\Clients\View\Form\Card\LimitType;

use App\Clients\Domain\Fuel\Type\Type;
use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;

final class ServiceLimitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('id', EntityType::class, [
                'class' => Type::class,
                'required' => true,
                'label' => 'label.service',
                'placeholder' => 'select.service',
                'attr' => [
                    'class' => 'col-sm-4 selectpicker disabled',
                ],
                'choice_label' => 'fuelName',
                'query_builder' => function (EntityRepository $entityRepository) {
                    $alias = 'e';
                    $qb = $entityRepository->createQueryBuilder($alias);

                    $qb
                        ->andWhere("$alias.fuelType = :fuelType")
                        ->andWhere("$alias.purseCode > 0")
                        ->setParameter('fuelType', FuelType::service()->getValue())
                        ->orderBy("$alias.fuelName", 'ASC');

                    return $qb;
                },
            ])
            ->add('dayLimit', NumberType::class, [
                'label' => 'label.day_l',
                'html5' => true,
                'attr' => [
                    'class' => 'col-sm-2',
                ],
            ])
            ->add('weekLimit', NumberType::class, [
                'label' => 'label.week_l',
                'html5' => true,
                'attr' => [
                    'class' => 'col-sm-2',
                ],
            ])
            ->add('monthLimit', NumberType::class, [
                'label' => 'label.month_l',
                'html5' => true,
                'attr' => [
                    'class' => 'col-sm-2',
                ],
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'collection_item';
    }
}
