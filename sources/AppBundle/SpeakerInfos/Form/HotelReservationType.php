<?php

namespace AppBundle\SpeakerInfos\Form;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Speaker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class HotelReservationType extends AbstractType
{
    const NIGHT_NONE = 'none';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /**
         * @var Event $event
         */
        $event = $options['event'];

        if (null === $event) {
            throw new \LogicException('Event has not been defined');
        }

        $choices = [];

        $start = \DateTimeImmutable::createFromMutable($event->getDateStart());
        $end = \DateTimeImmutable::createFromMutable($event->getDateEnd());


        $choices['Nuit du ' . $start->modify('-1 day')->format('d/m') . ' au ' . $start->format('d/m')] = Speaker::NIGHT_BEFORE;
        $choices['Nuit du ' . $start->format('d/m') . ' au ' . $end->format('d/m')] = Speaker::NIGHT_BETWEEN;
        $choices['Nuit du ' . $end->format('d/m') . ' au ' . $end->modify('+1 day')->format('d/m')] = Speaker::NIGHT_AFTER;
        $choices['Aucune nuité'] = self::NIGHT_NONE;

        $builder
            ->add(
                'nights',
                ChoiceType::class,
                [
                    'label' => "Nuités d'hotel",
                    'expanded' => true,
                    'multiple' => true,
                    'choices' => $choices,
                    'constraints' => [
                        new Choice(['choices' => array_values($choices), 'multiple' => true, 'min' => 1]),
                        new Callback(['callback' => function ($values, ExecutionContextInterface $context) {
                            if (in_array(HotelReservationType::NIGHT_NONE, $values)
                            && 1 !== count($values)) {
                                $context
                                    ->buildViolation('Impossible de choisir à la fois aucune nuité et une nuité')
                                    ->addViolation()
                                ;
                            }
                        }])
                    ]
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'event' => null,
            'csrf_protection' => false
        ]);
    }
}
