<?php

namespace App\Clients\View\Profile;

use App\Clients\Domain\Document\ValueObject\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;

final class UploadDocumentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'required' => true,
                'label' => 'label.type',
                'choices' => array_combine(Type::getNames(), Type::getNames()),
            ])
            ->add('document', FileType::class, [
                'required' => true,
                'label' => 'label.document',
                'attr' => [
                    'accept' => 'application/pdf, application/vnd.sealed.xls, application/vnd.ms-excel',
                ],
                'help' => 'Max. file size: 10M. Allowed types: *.pdf, *.xls',
            ])
        ;
    }
}
