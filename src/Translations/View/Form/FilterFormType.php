<?php

declare(strict_types=1);

namespace App\Translations\View\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FilterFormType extends AbstractType
{
    /**
     * @var array
     */
    private $locales;

    /**
     * @var string
     */
    private $defaultLocale;

    public function __construct(array $locales, $defaultLocale = 'en')
    {
        $this->locales = $locales;
        $this->defaultLocale = $defaultLocale;
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $map = [];
        foreach ($this->locales as $l) {
            $map[$l] = $l;
        }

        $builder
            ->add('key', TextType::class, [
                'required' => false,
                'label' => 'label.key',
            ])
            ->add('message', TextType::class, [
                'required' => false,
                'label' => 'label.translation',
            ])
            ->add('locale', ChoiceType::class, [
                'label' => 'label.locale',
                'data' => $this->defaultLocale,
                'choices' => $map,
            ]);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'empty_data' => [
                'locale' => $this->defaultLocale,
            ],
            'data_class' => null,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}
