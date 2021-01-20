<?php

namespace App\Clients\View\Form\Transaction;

use App\Clients\Domain\Fuel\Type\ValueObject\FuelType;
use App\Clients\Domain\Transaction\Card\ValueObject\Type;
use App\Clients\Infrastructure\Transaction\Repository\TransactionRepository;
use Infrastructure\Interfaces\Repository\Repository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class FilterFormType extends AbstractType
{
    /**
     * @var Repository
     */
    private $fuelTypeRepository;

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;
    /**
     * @var Request
     */
    private $request;

    public function __construct(
        Repository $fuelTypeRepository,
        TransactionRepository $transactionRepository,
        RequestStack $requestStack
    ) {
        $request = $requestStack->getCurrentRequest();

        if (!$request instanceof Request) {
            throw new \InvalidArgumentException('Request no found');
        }
        $this->request = $request;

        $this->fuelTypeRepository = $fuelTypeRepository;
        $this->transactionRepository = $transactionRepository;
    }

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
            ->add('azsName', TextType::class, [
                'required' => false,
                'label' => 'label.network_station',
            ])
            ->add('dateFrom', DateType::class, [
                'required' => false,
                'label' => 'label.date_from',
                'input' => 'string',
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'html5' => false,
                'format' => 'dd-mm-yyyy',
            ])
            ->add('dateTo', DateType::class, [
                'required' => false,
                'label' => 'label.date_to',
                'input' => 'string',
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'html5' => false,
                'format' => 'dd-mm-yyyy',
            ])
            ->add('type', ChoiceType::class, [
                'required' => false,
                'label' => 'label.type',
                'choices' => array_combine(Type::getNames(), Type::getNames()),
            ])
            ->add('supplyTypes', ChoiceType::class, [
                'label' => 'label.supply_types',
                'required' => false,
                'multiple' => true,
                'attr' => [
                    'class' => 'selectpicker',
                ],
                'choices' => array_combine(FuelType::getNames(), FuelType::getNames()),
            ])
            ->add('supplies', ChoiceType::class, [
                'required' => false,
                'label' => 'label.supplies',
                'multiple' => true,
                'choice_translation_domain' => false,
                'disabled' => true,
                'attr' => [
                    'class' => 'selectpicker',
                ],
                'placeholder' => 'select.supplies',
            ])
            ->add('limit', HiddenType::class, [
                'data' => '25',
            ]);
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();

        $supplyTypes = $this->request->get('supplyTypes');

        if (!$supplyTypes || empty($supplyTypes)) {
            return;
        }

        $fuelTypeCodes = $this->transactionRepository->getFuelCodes();

        $typeNames = FuelType::getNames();
        $typesValues = [];
        foreach ($supplyTypes as $selectedType) {
            if (in_array($selectedType, $typeNames)) {
                $typesValues[] = FuelType::fromName($selectedType)->getValue();
            }
        }
        $criteria = [
            'fuelCode_in' => $fuelTypeCodes,
            'fuelType_in' => $typesValues,
        ];

        $supplies = $this->fuelTypeRepository->findMany($criteria, ['fuelName' => 'ASC']);

        $supplyChoices = [];
        foreach ($supplies as $supply) {
            $supplyChoices[$supply->getFuelName()] = $supply->getFuelCode();
        }

        $form->add('supplies', ChoiceType::class, [
            'required' => false,
            'label' => 'label.supplies',
            'multiple' => true,
            'choice_translation_domain' => false,
            'attr' => [
                'class' => 'selectpicker',
            ],
            'placeholder' => 'select.supplies',
            'choices' => $supplyChoices,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
