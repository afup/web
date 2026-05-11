<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Form;

use AppBundle\Accounting\TvaTaux;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('reference', TextType::class, [
            'label' => 'Référence',
            'required' => true,
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
                new Assert\Length(max: 20),
            ],
        ])->add('designation', TextareaType::class, [
            'label' => 'Désignation',
            'required' => true,
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
                new Assert\Length(max: 100),
            ],
        ])->add('quantite', IntegerType::class, [
            'label' => 'Quantité par défaut',
            'required' => false,
            'constraints' => [
                new Assert\Positive(),
            ],
        ])->add('prixUnitaireHt', NumberType::class, [
            'label' => 'Prix unitaire HT',
            'required' => true,
            'scale' => 2,
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Positive(),
            ],
        ])->add('tauxTva', EnumType::class, [
            'label' => 'Taux de TVA',
            'class' => TvaTaux::class,
            'required' => true,
            'expanded' => true,
            'multiple' => false,
            'choice_label' => fn (TvaTaux $taux) => $taux->label(),
        ]);
    }
}
