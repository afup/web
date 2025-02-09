<?php

declare(strict_types=1);

namespace AppBundle\SpeakerInfos\Form;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Speaker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class HotelReservationType extends AbstractType
{
    const NIGHT_NONE = 'none';

    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
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


        $nights = [
            Speaker::NIGHT_BEFORE => ['from' => $start->modify('-1 day'), 'to' => $start],
            Speaker::NIGHT_BETWEEN => ['from' => $start, 'to' => $end],
            Speaker::NIGHT_AFTER => ['from' => $end, 'to' => $end->modify('+1 day')],
        ];

        foreach ($nights as $code => $infos) {
            $label = $this->translator->trans("Nuit du %date_from% au %date_to%", ['%date_from%' => $infos['from']->format('d/m'), '%date_to%' => $infos['to']->format('d/m')]);
            $choices[$label] = $code;
        }

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
                        new Callback(['callback' => function ($values, ExecutionContextInterface $context): void {
                            if (in_array(self::NIGHT_NONE, $values)
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

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'event' => null,
            'csrf_protection' => false
        ]);
    }
}
