<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Ticket;
use AppBundle\Event\Model\TicketOffer;
use AppBundle\Event\Ticket\TicketOffers;
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
        $event = $options['event'];
        $ticketOffers = $options['offers'];
        usort($ticketOffers, static function (TicketOffer $a, TicketOffer $b) {
            return $a->name > $b->name ? 1 : -1;
        });

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
            ])
            ->add('optin', BooleanType::class, [
                'label' => "Je souhaite recevoir des informations de la part de vos partenaires presse/media",
            ])
            ->add('ticketTypeId', ChoiceType::class, [
                'label' => 'Formule',
                'choices' => $ticketOffers,
                'choice_label' => fn(TicketOffer $offer) => sprintf('%s - [%d €]', $offer->name, $offer->price),
                'choice_value' => fn(TicketOffer|null $offer) => $offer->ticketTypeId ?? null,
                'group_by' => fn(TicketOffer $choice) => $choice->event ? 'Offre de l\'évènement' : 'Offre global',
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
                function ($ticketOffer): int {
                    return $ticketOffer->ticketTypeId;
                }
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
            'event' => Event::class,
            'offers' => TicketOffer::class.'[]',
        ]);
    }
}
