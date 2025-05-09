<?php

declare(strict_types=1);

namespace AppBundle\SpeakerInfos\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpeakersDinerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'will_attend',
                ChoiceType::class,
                [
                    'label' => 'Serez-vous des nôtres ?',
                    'expanded' => true,
                    'choices' => [
                        'Oui' => 1,
                        'Non' => 0,
                    ],
                ]
            )
            ->add(
                'has_special_diet',
                ChoiceType::class,
                [
                    'label' => 'Régime alimentaire',
                    'expanded' => true,
                    'choices' => [
                        "Non, je n'ai pas de régime alimentaire particulier" => 0,
                        "J'ai un régime alimentaire particulier / des contraintes alimentaires" => 1,
                    ],
                ]
            )
            ->add(
                'special_diet_description',
                TextareaType::class,
                [
                    'label' => 'Précisions sur le régime alimentaire',
                    'required' => false,
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
