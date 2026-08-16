<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Form;

use AppBundle\Accounting\Entity\Account;
use AppBundle\Accounting\Entity\Category;
use AppBundle\Accounting\Entity\Event;
use AppBundle\Accounting\Entity\Operation;
use AppBundle\Accounting\Entity\Payment;
use AppBundle\Accounting\Entity\Repository\AccountRepository;
use AppBundle\Accounting\Entity\Repository\CategoryRepository;
use AppBundle\Accounting\Entity\Repository\EventRepository;
use AppBundle\Accounting\Entity\Repository\OperationRepository;
use AppBundle\Accounting\Entity\Repository\PaymentRepository;
use AppBundle\Accounting\Entity\Transaction;
use AppBundle\Accounting\TvaZone;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class TransactionType extends AbstractType
{
    public function __construct(
        private readonly OperationRepository $operationRepository,
        private readonly AccountRepository $accountRepository,
        private readonly PaymentRepository $paymentRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly EventRepository $eventRepository,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('operation', EntityType::class, [
            'label' => 'Type d\'opération',
            'class' => Operation::class,
            'choices' => $this->operationRepository->findAll(),
            'choice_label' => 'name',
            'placeholder' => '',
            'constraints' => [
                new Assert\NotBlank(message: "Type d'opération manquant"),
            ],
        ])
        ->add('compte', EntityType::class, [
            'label' => 'Compte',
            'class' => Account::class,
            'choices' => $this->accountRepository->getAllSortedByName(),
            'choice_label' => 'name',
            'placeholder' => '',
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ])
        ->add('evenement', EntityType::class, [
            'label' => 'Évènement',
            'class' => Event::class,
            'choices' => $this->eventRepository->getAllSortedByName(),
            'choice_label' => 'name',
            'placeholder' => '',
            'constraints' => [
                new Assert\NotBlank(message: "Évènement manquant"),
            ],
        ])
        ->add('dateEcriture', DateType::class, [
            'label' => 'Date saisie',
            'widget' => 'single_text',
            'required' => false,
        ])
        ->add('categorie', EntityType::class, [
            'label' => 'categorie',
            'class' => Category::class,
            'placeholder' => '',
            'choices' => $this->categoryRepository->getAllSortedByName(),
            'choice_label' => 'name',
            'constraints' => [
                new Assert\NotBlank(message: "Catégorie manquante"),
            ],
        ])
        ->add('nomFournisseur', TextType::class, [
            'label' => 'Nom fournisseurs ',
            'required' => false,
            'empty_data' => '',
        ])
        ->add('tvaIntra', TextType::class, [
            'label' => 'TVA intracommunautaire (facture)',
            'required' => false,
        ])
        ->add('numero', TextType::class, [
            'label' => 'Numero facture',
            'required' => false,
            'empty_data' => '',
        ])
        ->add('description', TextareaType::class, [
            'label' => 'Description',
            'required' => false,
            'empty_data' => '',
        ])
        ->add('montant', MoneyType::class, [
            'label' => 'Montant',
            'currency' => '',
            'constraints' => [
                new Assert\NotBlank(message: "Montant manquant"),
                new Assert\NotEqualTo(value: 0, message: "Montant manquant"),
            ],
        ])
        ->add('commentaire', TextType::class, [
            'label' => 'Commentaire',
            'required' => false,
        ])
        ->add('montantTva5_5', MoneyType::class, [
            'label' => 'Montant HT soumis à TVA 5.5%',
            'required' => false,
            'currency' => '',
        ])
        ->add('montantTva10', MoneyType::class, [
            'label' => 'Montant HT soumis à TVA 10%',
            'required' => false,
            'currency' => '',
        ])
        ->add('montantTva20', MoneyType::class, [
            'label' => 'Montant HT soumis à TVA 20%',
            'required' => false,
            'currency' => '',
        ])
        ->add('montantTva0', MoneyType::class, [
            'label' => 'Montant HT non soumis à TVA',
            'required' => false,
            'currency' => '',
        ])
        ->add('tvaZone', EnumType::class, [
            'label' => 'Zone TVA',
            'required' => false,
            'class' => TvaZone::class,
            'placeholder' => 'Non définie',
            'choice_filter' => fn(?TvaZone $zone): bool => $zone !== TvaZone::Undefined,
            'choice_label' => fn(TvaZone $choice, string $key, mixed $value): string => $choice->getLabel(),

        ])
        ->add('modeReglement', EntityType::class, [
            'label' => 'Réglement',
            'class' => Payment::class,
            'required' => false,
            'placeholder' => '',
            'choices' => $this->paymentRepository->getAllSortedByName(),
            'choice_label' => 'name',
        ])
        ->add('dateReglement', DateType::class, [
            'label' => 'Date',
            'widget' => 'single_text',
            'required' => false,
        ])
        ->add('commentaireReglement', TextType::class, [
            'label' => 'Info réglement',
            'required' => false,
            'empty_data' => '',
        ])
        ->add('submit', SubmitType::class, [
            'label' => $options['operation'] === 'add' ? 'Ajouter' : 'Modifier',
            'attr' => [
                'class' => 'ui primary button',
            ],
        ]);

        if ($options['operation'] === 'edit' && $options['nextTransaction'] instanceof Transaction) {
            $builder->add('submitAndPass', SubmitType::class, [
                'label' => 'Soumettre et passer',
                'attr' => [
                    'class' => 'ui primary button',
                ],
            ])
            ->add('pass', SubmitType::class, [
                'label' => 'Passer',
                'attr' => [
                    'class' => 'ui primary button',
                ],
            ]);
        }

        $builder->get('montant')->resetViewTransformers();
        $builder->get('montant')->addViewTransformer(
            new MoneyToLocalizedStringTransformer(2, false, null, null, 'en'),
        );
        $builder->get('montantTva0')->resetViewTransformers();
        $builder->get('montantTva0')->addViewTransformer(
            new MoneyToLocalizedStringTransformer(2, false, null, null, 'en'),
        );
        $builder->get('montantTva5_5')->resetViewTransformers();
        $builder->get('montantTva5_5')->addViewTransformer(
            new MoneyToLocalizedStringTransformer(2, false, null, null, 'en'),
        );
        $builder->get('montantTva10')->resetViewTransformers();
        $builder->get('montantTva10')->addViewTransformer(
            new MoneyToLocalizedStringTransformer(2, false, null, null, 'en'),
        );
        $builder->get('montantTva20')->resetViewTransformers();
        $builder->get('montantTva20')->addViewTransformer(
            new MoneyToLocalizedStringTransformer(2, false, null, null, 'en'),
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'operation' => 'add',
            'data_class' => Transaction::class,
            'attr' => [
                'novalidate' => 'novalidate',
            ],
            'nextTransaction' => null,
        ]);
    }
}
