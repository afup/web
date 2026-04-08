<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Form;

use Afup\Site\Utils\Pays;
use AppBundle\Accounting\InvoicingCurrency;
use AppBundle\Accounting\InvoicingPaymentStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class InvoiceType extends AbstractType
{
    public function __construct(private readonly Pays $pays) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('invoiceDate', DateType::class, [
            'label' => 'Date facture',
            'widget' => 'single_text',
        ])->add('company', TextType::class, [
            'label' => 'Société',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(max: 50),
            ],
        ])->add('service', TextType::class, [
            'label' => 'Service',
            'required' => false,
            'empty_data' => '',
            'constraints' => [
                new Assert\Length(max: 50),
            ],
        ])->add('address', TextareaType::class, [
            'label' => 'Adresse',
        ])->add('zipcode', TextType::class, [
            'label' => 'Code postal',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(max: 10),
            ],
        ])->add('city', TextType::class, [
            'label' => 'Ville',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(max: 50),
            ],
        ])->add('countryId', ChoiceType::class, [
            'label' => 'Pays',
            'choices' => array_flip($this->pays->obtenirPays()),
        ])->add('lastname', TextType::class, [
            'label' => 'Nom',
            'required' => false,
            'empty_data' => '',
            'constraints' => [
                new Assert\Length(max: 50),
            ],
        ])->add('firstname', TextType::class, [
            'label' => 'Prénom',
            'required' => false,
            'empty_data' => '',
            'constraints' => [
                new Assert\Length(max: 50),
            ],
        ])->add('phone', TextType::class, [
            'label' => 'Tel',
            'required' => false,
            'empty_data' => '',
            'constraints' => [
                new Assert\Length(max: 30),
            ],
        ])->add('email', EmailType::class, [
            'label' => 'Email (facture)',
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Length(max: 100),
            ],
        ])->add('tvaIntra', TextType::class, [
            'label' => 'TVA intracommunautaire (facture)',
            'required' => false,
            'constraints' => [
                new Assert\Length(max: 20),
            ],
        ])->add('refClt1', TextType::class, [
            'label' => 'Référence client',
            'required' => false,
            'empty_data' => '',
            'constraints' => [
                new Assert\Length(max: 50),
            ],
        ])->add('refClt2', TextType::class, [
            'label' => 'Référence client 2',
            'required' => false,
            'empty_data' => '',
            'constraints' => [
                new Assert\Length(max: 50),
            ],
        ])->add('refClt3', TextType::class, [
            'label' => 'Référence client 3',
            'required' => false,
            'empty_data' => '',
            'constraints' => [
                new Assert\Length(max: 50),
            ],
        ])->add('observation', TextareaType::class, [
            'required' => false,
            'empty_data' => '',
            'label' => 'Observation',
        ])->add('currency', EnumType::class, [
            'required' => false,
            'class' => InvoicingCurrency::class,
            'attr' => ['size' => count(InvoicingCurrency::cases())],
            'label' => 'Monnaie de la facture',
            'placeholder' => false,
        ])->add('details', CollectionType::class, [
            'entry_type' => InvoicingRowType::class,
            'keep_as_list' => true,
            'allow_add' => false,
            'allow_delete' => false,
            'entry_options' => ['disabled' => true],
        ])->add('quotationNumber', TextType::class, [
            'label' => 'Numéro de devis',
            'required' => false,
            'attr' => ['readonly' => 'readonly'],
            'constraints' => [
                new Assert\Length(max: 50),
            ],
        ])->add('invoiceNumber', TextType::class, [
            'label' => 'Numéro facture',
            'required' => false,
            'attr' => ['readonly' => 'readonly'],
            'constraints' => [
                new Assert\Length(max: 50),
            ],
        ])->add('paymentStatus', EnumType::class, [
            'required' => false,
            'class' => InvoicingPaymentStatus::class,
            'attr' => ['size' => count(InvoicingPaymentStatus::cases())],
            'label' => 'État paiement',
            'placeholder' => false,
            'choice_label' => fn(InvoicingPaymentStatus $choice, string $key, mixed $value): string => $choice->label(),
        ])
        ->add('paymentDate', DateType::class, [
            'label' => 'Date de paiement',
            'required' => false,
            'widget' => 'single_text',
        ]);
    }
}
