<?php

namespace App\Clients\View\Form\ReplacementFuelType;

use App\Media\View\Form\Type\CroppedImageFormType;
use App\Users\Domain\User\ValueObject\Role;
use App\Users\Domain\User\ValueObject\Status;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ReplacementFuelTypeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('fuelCode', TextType::class, [
                'required' => false,
                'label' => 'label.fuelCode',
            ])
            ->add('fuelReplacementCode', TextType::class, [
                'required' => false,
                'label' => 'label.fuelReplacementCode',
            ])
        ;
    }
}
