<?php


namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Talk;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TalkType extends AbstractType
{
    const OPT_COC_CHECKED = 'codeOfConductChecked';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('abstract', TextareaType::class, ['label' => 'Résumé', 'required' => false])
            ->add(
                'staffNotes',
                TextareaType::class,
                    [
                        'label' => 'Notes aux organisateurs **',
                        'required' => false,
                    ]
            )
            ->add(
                'type',
                ChoiceType::class,
                ['label' => 'Type', 'choices' =>
                    [
                        'Conférence plénière (40 mn)' =>  Talk::TYPE_FULL_LONG,
                        'Conférence plénière (20 mn)' => Talk::TYPE_FULL_SHORT,
                    ]
                ]
            )
            ->add(
                'skill',
                ChoiceType::class,
                ['label' => 'Niveau requis', 'choices' =>
                    [
                        'Débutant' =>  Talk::SKILL_JUNIOR,
                        'Intermédiaire' => Talk::SKILL_MEDIOR,
                        'Avancé' => Talk::SKILL_SENIOR,
                        'N/A' => Talk::SKILL_NA
                    ]
                ]
            )
            ->add(
                'withWorkshop',
                CheckboxType::class,
                [
                    'label' => "Je propose de faire un atelier",
                    'required' => false,
                ]
            )
            ->add('workshopAbstract', TextareaType::class, ['label' => 'Résumé de l\'atelier',
                'required' => false,
            ])
            ->add(
                'needsMentoring',
                CheckboxType::class,
                [
                    'label' => "Je souhaite profiter du programme d'accompagnement des jeunes speakers",
                    'required' => false
                ]
            )
            ->add(
                'codeOfConduct',
                CheckboxType::class,
                [
                    'label' => 'J\'accepte le code de conduite et les conditions générales de participation*',
                    'mapped' => false,
                    'required' => true,
                    'data' => $options[self::OPT_COC_CHECKED]
                ]
            )
            ->add('save', SubmitType::class, ['label' => 'Sauvegarder'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            self::OPT_COC_CHECKED => false
        ]);
    }
}
