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

class TalkType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('abstract', TextareaType::class, ['label' => 'Résumé'])
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
                    'label' => 'J\'accepte le code de conduite *',
                    'mapped' => false,
                    'required' => true
                ]
            )
            ->add('save', SubmitType::class, ['label' => 'Sauvegarder'])
        ;
    }
}
