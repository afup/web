<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Entity\Interview;
use AppBundle\Event\Entity\Speaker;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Count;

/**
 * @extends AbstractType<Interview>
 */
class InterviewType extends AbstractType
{
    /**
     * @param array{available_speakers: array<Speaker>} $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('speakers', EntityType::class, [
                'required' => true,
                'label' => 'Speaker(s)',
                'class' => Speaker::class,
                'choice_label' => 'label',
                'choices' => $options['available_speakers'],
                'attr' => ['size' => count($options['available_speakers'])],
                'multiple' => true,
                'expanded' => false,
                'constraints' => [
                    new Count(min: 1, minMessage: 'Sélectionnez au moins un speaker.'),
                ],
                'help' => 'Il est possible de sélectionner plusieurs speakers pour une même interview.',
            ])
            ->add('datePublication', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de publication',
                'required' => true,
                'help' => "Une date dans le passé déclenche une publication immédiate. Une date dans le futur créée l'interview en status Planifié. Si cette date est modifiée ici, elle le sera aussi sur WordPress.",
            ])
            ->add('questions', CollectionType::class, [
                'entry_type' => InterviewQuestionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'label' => false,
                'constraints' => [
                    new Count(min: 1, minMessage: 'Il faut au moins une question.'),
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
            $interview = $event->getData();
            if (!$interview instanceof Interview) {
                return;
            }

            $position = 0;
            foreach ($interview->questions as $question) {
                $question->position = $position++;
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Interview::class,
        ]);

        $resolver->setRequired('available_speakers');
        $resolver->setAllowedTypes('available_speakers', 'array');
    }
}
