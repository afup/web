<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Form;

use AppBundle\Accounting\Model\Repository\CategoryRepository;
use AppBundle\Accounting\Model\Repository\EventRepository;
use AppBundle\Model\ComptaModeReglement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RuleType extends AbstractType
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly EventRepository $eventRepository,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $categories = [];
        $categories[''] = null;
        foreach ($this->categoryRepository->getAllSortedByName() as $category) {
            $categories[$category->getName()] = $category->getId();
        }

        $events = [];
        $events[''] = null;
        foreach ($this->eventRepository->getAllSortedByName() as $event) {
            $events[$event->getName()] = $event->getId();
        }

        $builder->add('label', TextType::class, [
            'label' => 'Nom de la régle',
            'required' => true,
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
                new Assert\Length(max: 255),
            ],
        ])->add('condition', TextType::class, [
            'label' => 'Condition',
            'required' => true,
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
                new Assert\Length(max: 255),
            ],
        ])->add('isCredit', ChoiceType::class, [
            'label' => 'Crédit/Débit ?',
            'choices' => [
                'Crédit' => true,
                'Débit' => false,
            ],
            'placeholder' => 'Les deux',
            'required' => false,
        ])->add('paymentTypeId', ChoiceType::class, [
            'label' => 'Mode de règlement',
            'placeholder' => false,
            'choices' => ['N.C.' => null] + array_flip(ComptaModeReglement::list()),
            'required' => false,
        ])->add('vat', ChoiceType::class, [
            'label' => 'Taux de TVA',
            'placeholder' => false,
            'choices' => ['N.C.' => '', '0%' => '0', '5.5%' => '5_5', '10%' => '10', '20%' => '20'],
            'required' => false,
        ])->add('categoryId', ChoiceType::class, [
            'label' => 'Catégorie',
            'choices' => $categories,
            'required' => false,
        ])->add('eventId', ChoiceType::class, [
            'label' => 'Évènement',
            'choices' => $events,
            'required' => false,
        ])->add('attachmentRequired', ChoiceType::class, [
            'label' => ' Justificatif obligatoire ? ',
            'choices' => ['Oui' => true, 'Non' => false],
            'placeholder' => 'N.C',
            'required' => false,
        ]);
    }
}
