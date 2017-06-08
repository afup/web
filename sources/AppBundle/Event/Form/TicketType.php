<?php

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Ticket\TicketTypeAvailability;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    const MEMBER_NOT = 0;
    const MEMBER_PERSONAL = 1;
    const MEMBER_CORPORATE = 2;

    /**
     * @var TicketEventTypeRepository
     */
    private $ticketEventTypeRepository;

    /**
     * @var EventRepository
     */
    private $eventRepository;

    /**
     * @var TicketTypeAvailability
     */
    private $ticketTypeAvailability;

    public function __construct(EventRepository $eventRepository, TicketEventTypeRepository $ticketEventTypeRepository, TicketTypeAvailability $ticketTypeAvailability)
    {
        $this->eventRepository = $eventRepository;
        $this->ticketEventTypeRepository = $ticketEventTypeRepository;
        $this->ticketTypeAvailability = $ticketTypeAvailability;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $eventTickets = null;
        if ($options['event_id'] !== null) {
            $event = $this->eventRepository->get($options['event_id']);
            $eventTickets = $this->ticketEventTypeRepository->getTicketsByEvent($event);
        }
        if ($eventTickets === null) {
            throw new RuntimeException(sprintf('Could not find tickets configuration for event %s', $options['event_id']));
        }

        $builder
            ->add('civility', ChoiceType::class, [
                'label' => 'Civilité',
                'choices' => [
                    'M.' => 'M.',
                    'Mlle' => 'Mlle',
                    'Mme' => 'Mme'
                ]
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom'
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom'
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email'
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Téléphone'
            ])
            ->add('ticketType', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => $eventTickets,
                'choice_label' => 'ticketType.prettyName',
                'choice_attr' => function(\AppBundle\Event\Model\TicketEventType $type, $key, $index) use ($options, $event) {
                    $attr = [
                        'data-description' => $type->getDescription(),
                        'data-price' => $type->getPrice(),
                        'data-date-end' => $type->getDateEnd()->format('Y-m-d H:i:s'),
                        'data-members-only' => (int)$type->getTicketType()->getIsRestrictedToMembers(),
                        'data-stock' => $this->ticketTypeAvailability->getStock($type, $event)
                    ];

                    if (
                        ($type->getTicketType()->getIsRestrictedToMembers() === true && $options['member_type'] === self::MEMBER_NOT)
                        ||
                        $attr['data-stock'] <= 0
                    ) {
                        $attr['disabled'] = 'disabled';
                    }
                    return $attr;
                }
            ])
            ->add('pmr', ChoiceType::class, [
                'label' => 'Mobilité réduite',
                'multiple' => false,
                'expanded' => true,
                'choices' => [
                    'Oui' => '1',
                    'Non' => '0'
                ]
            ])
            ->add('tag1', TextType::class, ['required' => false, 'attr' => ['placeholder' => 'Tag 1 ou Id Twitter (ex: @afup)']])
            ->add('tag2', TextType::class, ['required' => false, 'attr' => ['placeholder' => 'Tag 2']])
            ->add('tag3', TextType::class, ['required' => false, 'attr' => ['placeholder' => 'Tag 3']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
            'member_type' => self::MEMBER_NOT,
            'event_id' => null
        ]);
    }
}
