<?php

declare(strict_types=1);

namespace AppBundle\MembershipFee\Form;

use AppBundle\Controller\Admin\Membership\MembershipFeePayment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class MembershipFeeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('montant', NumberType::class, [
            'label' => 'Montant',
            'required' => true,
            'scale' => 2,
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\GreaterThanOrEqual(0),
            ],
        ])
        ->add('typeReglement', EnumType::class, [
            'class' => MembershipFeePayment::class,
            'label' => 'Type de réglement',
            'required' => true,
            'placeholder' => '',
            'constraints' => [
                new Assert\NotBlank(),
            ],
            'choice_label' => fn(MembershipFeePayment $choice, string $key, mixed $value): string => $choice->label(),
        ])
        ->add('informationsReglement', TextType::class, [
            'label' => 'Informations',
            'required' => false,
        ])
        ->add('referenceClient', TextType::class, [
            'label' => 'Référence client',
            'required' => false,
        ])
        ->add('dateDebut', DateType::class, [
            'required' => false,
            'widget' => 'single_text',
            'label' => 'Date début',
        ])
        ->add('dateFin', DateType::class, [
            'required' => false,
            'widget' => 'single_text',
            'label' => 'Date fin',
        ])
        ->add('commentaires', TextareaType::class, [
            'required' => false,
            'label' => 'Commentaires',
        ]);
    }
}
