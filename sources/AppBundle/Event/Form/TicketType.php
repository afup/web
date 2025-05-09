<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Antennes\AntennesCollection;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Model\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Repository\TicketSpecialPriceRepository;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Model\TicketEventType;
use AppBundle\Event\Ticket\TicketTypeAvailability;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    const MEMBER_NOT = 0;
    const MEMBER_PERSONAL = 1;
    const MEMBER_CORPORATE = 2;

    private readonly AntennesCollection $antennesCollection;

    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly TicketEventTypeRepository $ticketEventTypeRepository,
        private readonly TicketTypeAvailability $ticketTypeAvailability,
        private readonly TicketSpecialPriceRepository $ticketSpecialPriceRepository,
        private readonly TicketTypeRepository $ticketTypeRepository,
    ) {
        $this->antennesCollection = new AntennesCollection();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $eventTickets = null;
        $event = null;
        if ($options['event_id'] !== null) {
            $event = $this->eventRepository->get($options['event_id']);
            $eventTickets = $this->ticketEventTypeRepository->getTicketsByEvent($event, true, TicketEventTypeRepository::REMOVE_PAST_TICKETS);
        }

        if ($eventTickets === null) {
            throw new RuntimeException(sprintf('Could not find tickets configuration for event %s', $options['event_id']));
        }

        $builder
            ->add('civility', ChoiceType::class, [
                'label' => 'Civilité',
                'choices' => [
                    'M.' => 'M.',
                    'Mme' => 'Mme',
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('phoneNumber', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
            ])
            ->add('nearestOffice', ChoiceType::class, [
                'label' => 'Antenne de prédilection',
                'required' => false,
                'choices' => array_flip($this->antennesCollection->getOrderedLabelsByKey()),
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) use ($eventTickets, $options, $event): void {
            $filteredEventTickets = [];
            foreach ($eventTickets as $eventTicket) {
                if ($eventTicket->getTicketType()->getIsRestrictedToCfpSubmitter() && !$options['is_cfp_submitter']) {
                    continue;
                }
                $filteredEventTickets[] = $eventTicket;
            }

            if ($options['event_id'] !== null) {
                $event = $this->eventRepository->get($options['event_id']);
                $ticketSpecialPrice = $this->ticketSpecialPriceRepository->findUnusedToken($event, $options['special_price_token']);

                if (null !== $ticketSpecialPrice) {
                    $ticketType = $this->ticketTypeRepository->get(AFUP_FORUM_SPECIAL_PRICE);

                    $eToken = new TicketEventType();
                    $eToken->setDateStart($ticketSpecialPrice->getDateStart());
                    $eToken->setDateEnd($ticketSpecialPrice->getDateEnd());
                    $eToken->setPrice($ticketSpecialPrice->getPrice());
                    $eToken->setTicketType($ticketType);
                    $eToken->setEventId($ticketSpecialPrice->getEventId());
                    $eToken->setTicketTypeId(AFUP_FORUM_SPECIAL_PRICE);
                    $filteredEventTickets = [];
                    $filteredEventTickets[] = $eToken;
                }
            }

            $formEvent->getForm()->add('ticketEventType', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'label' => 'Formule',
                'choices' => $filteredEventTickets,
                'choice_label' => 'ticketType.prettyName',
                'error_bubbling' => false,
                'choice_attr' => function (TicketEventType $type, $key, $index) use ($options, $event): array {
                    $attr = [
                        'data-description' => $type->getDescription(),
                        'data-price' => $type->getPrice(),
                        'data-date-end' => $type->getDateEnd()->format('d/m'),
                        'data-date-end-raw' => $type->getDateEnd()->format('Y-m-d'),
                        'data-members-only' => (int) $type->getTicketType()->getIsRestrictedToMembers(),
                        'data-max-tickets' => $type->getMaxTickets(),
                        'data-stock' => $this->ticketTypeAvailability->getStock($type, $event),
                        'data-label' => $type->getTicketType()->getPrettyName(),
                    ];

                    if (
                        ($type->getTicketType()->getIsRestrictedToMembers() === true && $options['member_type'] === self::MEMBER_NOT)
                        ||
                        $attr['data-stock'] <= 0
                    ) {
                        $attr['disabled'] = 'disabled';
                    }
                    return $attr;
                },
            ])
            ;
        });

        if ($event->getTransportInformationEnabled()) {
            $transportMode = Ticket::TRANSPORT_MODES;
            asort($transportMode);

            $builder->add('transportMode', ChoiceType::class, [
                'label' => 'Quel est votre mode de transport ?',
                'required' => true,
                'choices' => ['' => ''] + array_flip($transportMode),
            ]);

            $builder->add('transportDistance', ChoiceType::class, [
                'label' => 'Quelle sera la distance parcourue ?',
                'required' => true,
                'choices' => ['' => ''] + array_flip(Ticket::TRANSPORT_DISTANCES),
            ]);
        }


        $builder
            ->add('tag1', TextType::class, ['required' => false, 'attr' => ['placeholder' => 'Tag 1 ou Id Twitter (ex: @afup)']])
            ->add('tag2', TextType::class, ['required' => false, 'attr' => ['placeholder' => 'Tag 2']])
            ->add('tag3', TextType::class, ['required' => false, 'attr' => ['placeholder' => 'Tag 3']])
            ->add('specialPriceToken', HiddenType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
            'member_type' => self::MEMBER_NOT,
            'is_cfp_submitter' => false,
            'event_id' => null,
            'special_price_token' => null,
        ]);
    }
}
