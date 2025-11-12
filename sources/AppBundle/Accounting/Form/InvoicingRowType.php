<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Form;

use AppBundle\Accounting\Model\InvoicingDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\MoneyToLocalizedStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class InvoicingRowType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('reference', TextType::class, [
            'label' => 'Référence',
            'required' => false,
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
                new Assert\Length(max: 20),
            ],
        ])->add('designation', TextareaType::class, [
            'label' => 'Désignation',
            'required' => false,
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
                new Assert\Length(max: 100),
            ],
        ])->add('quantity', NumberType::class, [
            'label' => 'Quantité',
            'required' => false,
            'scale' => 2,
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ])->add('unitPrice', NumberType::class, [
            'label' => 'Prix unitaire HT',
            'required' => false,
            'scale' => 2,
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ])->add('tva', ChoiceType::class, [
            'label' => 'Taux de TVA',
            'required' => false,
            'placeholder' => false,
            'choices' => ['Non soumis' => 0, '5.5%' => 5.50, '10%' => 10.00, '20%' => 20.00],
            'help' => 'Rappel : sponsoring 20%, place supplémentaire 10%.',
            'constraints' => [
                new Assert\NotBlank(),
            ],
        ]);
        $builder->get('unitPrice')->resetViewTransformers();
        $builder->get('unitPrice')->addViewTransformer(
            new MoneyToLocalizedStringTransformer(2, false, null, null, 'en'),
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InvoicingDetail::class,
        ]);
    }
}
