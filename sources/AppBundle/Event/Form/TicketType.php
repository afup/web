<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Antennes\Entity\Repository\AntenneRepository;
use AppBundle\Association\Genre;
use AppBundle\Event\Entity\BilleteriePrivee;
use AppBundle\Event\Entity\Repository\TicketTypeRepository;
use AppBundle\Event\Model\Repository\EventRepository;
use AppBundle\Event\Entity\Repository\TicketEventTypeRepository;
use AppBundle\Event\Entity\Repository\TicketSpecialPriceRepository;
use AppBundle\Event\Entity\Inscription;
use AppBundle\Event\Entity\TicketEventType;
use AppBundle\Event\Entity\TicketSpecialPrice;
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
use Symfony\Component\Validator\Constraints\Email;
use AppBundle\Event\Enum\DistanceTransport;
use AppBundle\Event\Enum\ModeTransport;
use AppBundle\Event\Enum\TypeInscription;

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
                'empty_data' => '',
                'label' => 'Prénom',
            ])
            ->add('lastname', TextType::class, [
                'empty_data' => '',
                'label' => 'Nom',
            ])
            ->add('email', EmailType::class, [
                'empty_data' => '',
                'label' => 'Email',
                'constraints' => [
                    new Email(),
                ],
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
                if ($eventTicket->ticketType->isRestrictedToCfpSubmitter && !$options['is_cfp_submitter']) {
                    continue;
                }
                $filteredEventTickets[] = $eventTicket;
            }

            $specialPriceToken = $options['special_price_token'];
            $ticketSpecialPrice = null;
            if (is_int($options['event_id'])) {
                $ticketSpecialPrice = $this->ticketSpecialPriceRepository->findUnusedToken(
                    $options['event_id'],
                    is_string($specialPriceToken) ? $specialPriceToken : null,
                );
            }

            if (null !== $ticketSpecialPrice) {
                $filteredEventTickets = $this->createSpecialPriceTicketEventType($ticketSpecialPrice, $filteredEventTickets);
            }

            $billeteriePrivee = $options['billeterie_privee'];
            $choiceLabel = 'ticketType.prettyName';
            if ($billeteriePrivee instanceof BilleteriePrivee) {
                $filteredEventTickets = $this->createBilleteriePriveeTicketEventType($billeteriePrivee, $filteredEventTickets);
                $typeDePlace = $this->ticketTypeRepository->find($billeteriePrivee->ticketTypeId);
                $choiceLabel = static fn(): string => $typeDePlace !== null ? $typeDePlace->prettyName : 'Billet';
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

                    $stock = null;
                    if ($type->ticketType !== null) {
                        $stock = $this->ticketTypeAvailability->getStock($type, $event);
                        $attr['data-members-only'] = (int) $type->ticketType->isRestrictedToMembers;
                        $attr['data-stock'] = $stock;
                        $attr['data-label'] = $type->ticketType->prettyName;
                    }

                    if (
                        ($type->ticketType?->isRestrictedToMembers === true && $options['member_type'] === self::MEMBER_NOT)
                        || ($stock !== null && $stock <= 0)
                    ) {
                        $attr['disabled'] = 'disabled';
                    }
                    return $attr;
                },
            ])
            ;
        });

        if ($event->getTransportInformationEnabled()) {
            $builder->add('transportMode', EnumType::class, [
                'label' => 'Quel est votre mode de transport ?',
                'required' => true,
                'placeholder' => '',
                'class' => ModeTransport::class,
                'choices' => ModeTransport::choixParLibelle(),
                'choice_label' => static fn(ModeTransport $mode): string => $mode->label(),
            ]);

            $builder->add('transportDistance', EnumType::class, [
                'label' => 'Quelle sera la distance parcourue ?',
                'required' => true,
                'placeholder' => '',
                'class' => DistanceTransport::class,
                'choice_label' => static fn(DistanceTransport $distance): string => $distance->label(),
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
            'data_class' => Inscription::class,
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
        $ticketType = $this->ticketTypeRepository->find(TypeInscription::PrixSpecial->value);
        if ($ticketType === null) {
            return $filteredEventTickets;
        }

        $eToken = new TicketEventType();
        $eToken->dateStart = \DateTime::createFromImmutable($ticketSpecialPrice->dateStart);
        $eToken->dateEnd = \DateTime::createFromImmutable($ticketSpecialPrice->dateEnd);
        $eToken->price = $ticketSpecialPrice->price ?? 0.0;
        $eToken->ticketType = $ticketType;
        $eToken->eventId = $ticketSpecialPrice->eventId;
        $eToken->ticketTypeId = TypeInscription::PrixSpecial->value;

        return [$eToken];
    }

    /**
     * @param TicketEventType[] $filteredEventTickets
     * @return TicketEventType[]
     */
    private function createBilleteriePriveeTicketEventType(BilleteriePrivee $billeteriePrivee, array $filteredEventTickets): array
    {
        $ticketType = $this->ticketTypeRepository->find(TypeInscription::PrixSpecial->value);
        if ($ticketType === null) {
            return $filteredEventTickets;
        }

        $eBilleterie = new TicketEventType();
        $eBilleterie->dateStart = \DateTime::createFromImmutable($billeteriePrivee->dateDebut);
        $eBilleterie->dateEnd = \DateTime::createFromImmutable($billeteriePrivee->dateFin);
        $eBilleterie->price = $billeteriePrivee->prix;
        $eBilleterie->ticketType = $ticketType;
        $eBilleterie->eventId = $billeteriePrivee->eventId;
        $eBilleterie->ticketTypeId = TypeInscription::PrixSpecial->value;

        return [$eBilleterie];
    }
}
