<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Form;

use AppBundle\Compta\Importer\CreditMutuel;
use AppBundle\Compta\Importer\CreditMutuelLivret;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class TransactionsImportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('file', FileType::class, [
            'label' => 'Fichier banque',
            'required' => true,
            'constraints' => [
                new Assert\File(mimeTypes: ['text/csv', 'text/plain'], mimeTypesMessage: 'Veuillez uploader un fichier CSV valide.'),
            ],
        ])->add('bankAccount', ChoiceType::class, [
            'label' => 'Banque',
            'required' => true,
            'choices' => [
                'Crédit Mutuel - Compte Courant' => CreditMutuel::CODE,
                'Crédit Mutuel - Livret' => CreditMutuelLivret::CODE,
            ],
        ]);
    }
}
