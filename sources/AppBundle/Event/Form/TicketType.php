<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Antennes\AntenneRepository;
use AppBundle\Association\Genre;
use AppBundle\Event\Entity\BilleteriePrivee;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Entity\Repository\TicketEventTypeRepository;
use AppBundle\Event\Model\Repository\TicketSpecialPriceRepository;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Entity\TicketEventType;
use AppBundle\Event\Model\TicketSpecialPrice;
use AppBundle\Event\Ticket\TicketTypeAvailability;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public const int MEMBER_NOT = 0;
    public const int MEMBER_PERSONAL = 1;
    public const int MEMBER_CORPORATE = 2;

    public function __construct(
        private readonly EventRepository $eventRepository,
        private readonly TicketEventTypeRepository $ticketEventTypeRepository,
        private readonly TicketTypeAvailability $ticketTypeAvailability,
        private readonly TicketSpecialPriceRepository $ticketSpecialPriceRepository,
        private readonly TicketTypeRepository $ticketTypeRepository,
        private readonly AntenneRepository $antenneRepository,
    ) {}

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
            ->add('genre', EnumType::class, [
                'required' => false,
                'class' => Genre::class,
                'label' => 'Genre',
                'placeholder' => 'Ne se prononce pas',
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
                'choices' => array_flip($this->antenneRepository->getOrderedLabelsByKey()),
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $formEvent) use ($eventTickets, $options, $event): void {
            $filteredEventTickets = [];
            foreach ($eventTickets as $eventTicket) {
                if ($eventTicket->ticketType->getIsRestrictedToCfpSubmitter() && !$options['is_cfp_submitter']) {
                    continue;
                }
                $filteredEventTickets[] = $eventTicket;
            }

            $event = $this->eventRepository->get($options['event_id']);
            $ticketSpecialPrice = $this->ticketSpecialPriceRepository->findUnusedToken($event, $options['special_price_token']);

            if (null !== $ticketSpecialPrice) {
                $filteredEventTickets = $this->createSpecialPriceTicketEventType($ticketSpecialPrice, $filteredEventTickets);
            }

            $billeteriePrivee = $options['billeterie_privee'];
            $choiceLabel = 'ticketType.prettyName';
            if ($billeteriePrivee instanceof BilleteriePrivee) {
                $filteredEventTickets = $this->createBilleteriePriveeTicketEventType($billeteriePrivee, $filteredEventTickets);
                $typeDePlace = $this->ticketTypeRepository->get($billeteriePrivee->ticketTypeId);
                $choiceLabel = static fn(): string => $typeDePlace !== null ? $typeDePlace->getPrettyName() : 'Billet';
            }

            $formEvent->getForm()->add('ticketEventType', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'label' => 'Formule',
                'choices' => $filteredEventTickets,
                'choice_label' => $choiceLabel,
                'error_bubbling' => false,
                'choice_attr' => function (TicketEventType $type, $key, $index) use ($options, $event): array {
                    $attr = [
                        'data-description' => $type->description,
                        'data-price' => $type->price,
                        'data-date-end' => $type->dateEnd->format('d/m'),
                        'data-date-end-raw' => $type->dateEnd->format('Y-m-d'),
                        'data-max-tickets' => $type->maxTickets,
                    ];

                    if ($type->ticketType !== null) {
                        $attr['data-members-only'] = (int) $type->ticketType->getIsRestrictedToMembers();
                        $attr['data-stock'] = $this->ticketTypeAvailability->getStock($type, $event);
                        $attr['data-label'] = $type->ticketType->getPrettyName();
                    }

                    if (
                        ($type->ticketType?->getIsRestrictedToMembers() === true && $options['member_type'] === self::MEMBER_NOT)
                        || (isset($attr['data-stock']) && $attr['data-stock'] <= 0)
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
            'billeterie_privee' => null,
        ]);
    }

    /**
     * @param TicketEventType[] $filteredEventTickets
     * @return TicketEventType[]
     */
    private function createSpecialPriceTicketEventType(TicketSpecialPrice $ticketSpecialPrice, array $filteredEventTickets): array
    {
        $ticketType = $this->ticketTypeRepository->get(Ticket::TYPE_SPECIAL_PRICE);
        if (!$ticketType instanceof \AppBundle\Event\Model\TicketType) {
            return $filteredEventTickets;
        }

        $dateStart = $ticketSpecialPrice->getDateStart();
        $dateEnd = $ticketSpecialPrice->getDateEnd();
        if ($dateStart === null || $dateEnd === null) {
            return $filteredEventTickets;
        }

        $eToken = new TicketEventType();
        $eToken->dateStart = $dateStart;
        $eToken->dateEnd = $dateEnd;
        $eToken->price = $ticketSpecialPrice->getPrice();
        $eToken->ticketType = $ticketType;
        $eToken->eventId = $ticketSpecialPrice->getEventId();
        $eToken->ticketTypeId = Ticket::TYPE_SPECIAL_PRICE;

        return [$eToken];
    }

    /**
     * @param TicketEventType[] $filteredEventTickets
     * @return TicketEventType[]
     */
    private function createBilleteriePriveeTicketEventType(BilleteriePrivee $billeteriePrivee, array $filteredEventTickets): array
    {
        $ticketType = $this->ticketTypeRepository->get(Ticket::TYPE_SPECIAL_PRICE);
        if (!$ticketType instanceof \AppBundle\Event\Model\TicketType) {
            return $filteredEventTickets;
        }

        $eBilleterie = new TicketEventType();
        $eBilleterie->dateStart = \DateTime::createFromImmutable($billeteriePrivee->dateDebut);
        $eBilleterie->dateEnd = \DateTime::createFromImmutable($billeteriePrivee->dateFin);
        $eBilleterie->price = $billeteriePrivee->prix;
        $eBilleterie->ticketType = $ticketType;
        $eBilleterie->eventId = $billeteriePrivee->eventId;
        $eBilleterie->ticketTypeId = Ticket::TYPE_SPECIAL_PRICE;

        return [$eBilleterie];
    }
}
