<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Model\TicketOffer;
use AppBundle\Form\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $ticketStatuses = [
            'Inscription créée' => Ticket::STATUS_CREATED,
            'Inscription annulée' => Ticket::STATUS_CANCELLED,
            'Paiement CB erreur' => Ticket::STATUS_ERROR,
            'Paiement CB refusé' => Ticket::STATUS_DECLINED,
            'Inscription réglée' => Ticket::STATUS_PAID,
            'Invitation' => Ticket::STATUS_GUEST,
            'Attente règlement' => Ticket::STATUS_WAITING,
            'Inscription confirmée' => Ticket::STATUS_CONFIRMED,
            'Inscription à posteriori' => Ticket::STATUS_PAID_AFTER,
        ];

        $invoiceStatuses = [
            'Facture à envoyer' => Ticket::INVOICE_TODO,
            'Facture envoyée' => Ticket::INVOICE_SENT,
            'Facture reçue' => Ticket::INVOICE_RECEIVED,
        ];

        $event = $options['event'];
        $ticketOffers = $options['offers'];
        usort($ticketOffers, static fn(TicketOffer $a, TicketOffer $b) => $a->name > $b->name ? 1 : -1);

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
            ->add('comments', TextareaType::class, [
                'label' => 'Commentaires',
                'required' => false,
            ])
            ->add('companyCitation', BooleanType::class, [
                'label' => "J'accepte que ma compagnie soit citée comme participant à la conférence",
            ])
            ->add('newsletter', BooleanType::class, [
                'label' => "Je souhaite être tenu au courant des rencontres de l'AFUP sur des sujets afférents à PHP",
                'required' => true,
            ])
            ->add('optin', BooleanType::class, [
                'label' => "Je souhaite recevoir des informations de la part de vos partenaires presse/media",
                'required' => true,
            ])
            ->add('ticketTypeId', ChoiceType::class, [
                'label' => 'Formule',
                'choices' => $ticketOffers,
                'choice_label' => fn(TicketOffer $offer) => sprintf('%s - [%d €]', $offer->name, $offer->price),
                'choice_value' => fn(?TicketOffer $offer) => $offer->ticketTypeId ?? null,
                'group_by' => fn(TicketOffer $choice) => $choice->event ? 'Offre de l\'évènement' : 'Offre global',
            ])
            ->add('status', ChoiceType::class, [
                'label' => "État de l'inscription",
                'choices' => $ticketStatuses,
                'required' => true,
            ])
            ->add('invoiceStatus', ChoiceType::class, [
                'label' => 'État de la facturation',
                'choices' => $invoiceStatuses,
                'required' => true,
            ]);

        if ($event->getTransportInformationEnabled()) {
            $transportMode = Ticket::TRANSPORT_MODES;
            asort($transportMode);

            $builder->add('transportMode', ChoiceType::class, [
                'label' => 'Quel est votre mode de transport ?',
                'required' => true,
                'choices' => ['' => null] + array_flip($transportMode),
            ]);

            $builder->add('transportDistance', ChoiceType::class, [
                'label' => 'Quelle sera la distance parcourue ?',
                'required' => true,
                'choices' => ['' => null] + array_flip(Ticket::TRANSPORT_DISTANCES),
            ]);
        }

        $builder->get('ticketTypeId')
            ->addModelTransformer(new CallbackTransformer(
                function ($id) use ($ticketOffers): TicketOffer|null {
                    foreach ($ticketOffers as $ticketOffer) {
                        if ($ticketOffer->ticketTypeId === $id) {
                            return $ticketOffer;
                        }
                    }
                    return null;
                },
                fn($ticketOffer): int => $ticketOffer->ticketTypeId,
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
            'event' => Event::class,
            'offers' => TicketOffer::class . '[]',
        ]);
    }
}
