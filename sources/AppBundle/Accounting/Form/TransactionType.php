<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Form;

use Afup\Site\Comptabilite\Comptabilite;
use AppBundle\Accounting\Entity\Event;
use AppBundle\Accounting\Entity\Repository\AccountRepository;
use AppBundle\Accounting\Entity\Repository\CategoryRepository;
use AppBundle\Accounting\Entity\Repository\EventRepository;
use AppBundle\Accounting\Entity\Repository\OperationRepository;
use AppBundle\Accounting\Entity\Repository\PaymentRepository;
use AppBundle\Accounting\Model\Transaction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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
        $builder->add('operationId', ChoiceType::class, [
            'label' => 'Type d\'opération',
            'choices' => $this->buildOperationTypeChoice(),
            'placeholder' => '',
            'constraints' => [
                new Assert\NotBlank(message: "Type d'opération manquant"),
            ],
        ])
        ->add('accountId', ChoiceType::class, [
            'label' => 'Compte',
            'choices' => $this->buildAccountChoice(),
            'placeholder' => '',
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ])
        ->add('eventId', ChoiceType::class, [
            'label' => 'Évènement',
            'choices' => $this->buildEventChoice(),
            'placeholder' => '',
            'constraints' => [
                new Assert\NotBlank(message: "Évènement manquant"),
            ],
        ])
        ->add('accountingDate', DateType::class, [
            'label' => 'Date saisie',
            'widget' => 'single_text',
            'required' => false,
        ])
        ->add('categoryId', ChoiceType::class, [
            'label' => 'categorie',
            'placeholder' => '',
            'choices' => $this->buildCategoryChoice(),
            'constraints' => [
                new Assert\NotBlank(message: "Catégorie manquante"),
            ],
        ])
        ->add('vendorName', TextType::class, [
            'label' => 'Nom fournisseurs ',
            'required' => false,
            'empty_data' => '',
        ])
        ->add('tvaIntra', TextType::class, [
            'label' => 'TVA intracommunautaire (facture)',
            'required' => false,
        ])
        ->add('number', TextType::class, [
            'label' => 'Numero facture',
            'required' => false,
            'empty_data' => '',
        ])
        ->add('description', TextareaType::class, [
            'label' => 'Description',
            'required' => false,
            'empty_data' => '',
        ])
        ->add('amount', MoneyType::class, [
            'label' => 'Montant',
            'currency' => '',
            'constraints' => [
                new Assert\NotBlank(message: "Montant manquant"),
                new Assert\NotEqualTo(value: 0, message: "Montant manquant"),
            ],
        ])
        ->add('comment', TextType::class, [
            'label' => 'Commentaire',
            'required' => false,
        ])
        ->add('amountTva5_5', MoneyType::class, [
            'label' => 'Montant HT soumis à TVA 5.5%',
            'required' => false,
            'currency' => '',
        ])
        ->add('amountTva10', MoneyType::class, [
            'label' => 'Montant HT soumis à TVA 10%',
            'required' => false,
            'currency' => '',
        ])
        ->add('amountTva20', MoneyType::class, [
            'label' => 'Montant HT soumis à TVA 20%',
            'required' => false,
            'currency' => '',
        ])
        ->add('amountTva0', MoneyType::class, [
            'label' => 'Montant HT non soumis à TVA',
            'required' => false,
            'currency' => '',
        ])
        ->add('tvaZone', ChoiceType::class, [
            'label' => 'Zone TVA',
            'required' => false,
            'choices' => array_flip(Comptabilite::TVA_ZONES),
            'placeholder' => 'Non définie',

        ])
        ->add('paymentTypeId', ChoiceType::class, [
            'label' => 'Réglement',
            'required' => false,
            'choices' => $this->buildPaymentTypeChoice(),
        ])
        ->add('paymentDate', DateType::class, [
            'label' => 'Date',
            'widget' => 'single_text',
            'required' => false,
        ])
        ->add('paymentComment', TextType::class, [
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

        $builder->get('amount')->resetViewTransformers();
        $builder->get('amount')->addViewTransformer(
            new MoneyToLocalizedStringTransformer(2, false, null, null, 'en'),
        );
        $builder->get('amountTva0')->resetViewTransformers();
        $builder->get('amountTva0')->addViewTransformer(
            new MoneyToLocalizedStringTransformer(2, false, null, null, 'en'),
        );
        $builder->get('amountTva5_5')->resetViewTransformers();
        $builder->get('amountTva5_5')->addViewTransformer(
            new MoneyToLocalizedStringTransformer(2, false, null, null, 'en'),
        );
        $builder->get('amountTva10')->resetViewTransformers();
        $builder->get('amountTva10')->addViewTransformer(
            new MoneyToLocalizedStringTransformer(2, false, null, null, 'en'),
        );
        $builder->get('amountTva20')->resetViewTransformers();
        $builder->get('amountTva20')->addViewTransformer(
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

    private function buildOperationTypeChoice(): array
    {
        $choices = [];
        foreach ($this->operationRepository->findAll() as $operation) {
            $choices[$operation->name] = $operation->id;
        }

        return $choices;
    }

    private function buildAccountChoice(): array
    {
        $choices = [];
        foreach ($this->accountRepository->getAllSortedByName() as $account) {
            $choices[$account->name] = $account->id;
        }

        return $choices;
    }

    private function buildEventChoice(): array
    {
        $choices = [];
        /** @var Event $event */
        foreach ($this->eventRepository->getAllSortedByName() as $event) {
            $choices[$event->name] = $event->id;
        }

        return $choices;
    }

    private function buildCategoryChoice(): array
    {
        $choices = [];
        foreach ($this->categoryRepository->getAllSortedByName() as $category) {
            $choices[$category->name] = $category->id;
        }

        return $choices;
    }

    private function buildPaymentTypeChoice(): array
    {
        $choices = [];
        $choices[''] = 0;
        foreach ($this->paymentRepository->getAllSortedByName() as $paymentType) {
            $choices[$paymentType->name] = $paymentType->id;
        }

        return $choices;
    }
}
