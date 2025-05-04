<?php

declare(strict_types=1);


namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Talk;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TalkType extends AbstractType
{
    const OPT_COC_CHECKED = 'codeOfConductChecked';
    const IS_AFUP_DAY = 'isAfupDay';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre (1)',
            ])
            ->add('abstract', TextareaType::class, [
                'label' => 'Résumé (1)',
                'required' => false,
            ])
            ->add('staffNotes', TextareaType::class, [
                'label' => 'Notes aux organisateurs (2)',
                'required' => false,
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type',
                'choices' => [
                    'Conférence plénière (40 mn)' => Talk::TYPE_FULL_LONG,
                    'Conférence plénière (20 mn)' => Talk::TYPE_FULL_SHORT,
                ],
            ])
            ->add('skill', ChoiceType::class, [
                'label' => 'Niveau requis',
                'choices' => [
                    'Débutant' => Talk::SKILL_JUNIOR,
                    'Intermédiaire' => Talk::SKILL_MEDIOR,
                    'Avancé' => Talk::SKILL_SENIOR,
                    'N/A' => Talk::SKILL_NA,
                ],
            ]);

        if (!$options[self::IS_AFUP_DAY]) {
            $builder->add('withWorkshop', CheckboxType::class, [
                'label' => "Je propose de faire un atelier",
                'required' => false,
                'help' => 'cfp_propose_workshop',
            ])
            ->add('workshopAbstract', TextareaType::class, ['label' => 'Résumé de l\'atelier',
                'required' => false,
            ]);
        }

        $builder->add('needsMentoring', CheckboxType::class, [
                'label' => "Je souhaite profiter du programme d'accompagnement des jeunes speakers",
                'required' => false,
            ])
            ->add('codeOfConduct', CheckboxType::class, [
                'label' => 'J\'accepte le code de conduite et les conditions générales de participation (1)',
                'mapped' => false,
                'required' => true,
                'data' => $options[self::OPT_COC_CHECKED],
            ])
            ->add('hasAllowedToSharingWithLocalOffices', ChoiceType::class, [
                'choices' => [
                    'J\'autorise' => true,
                    'Je refuse' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Autoriser l’AFUP à transmettre ma proposition de conférence à ses antennes locales ?',
                'help' => 'cfp_propose_workshop',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            self::OPT_COC_CHECKED => false,
            self::IS_AFUP_DAY => false,
        ]);
    }
}
