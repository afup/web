<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Association\Genre;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Entity\Inscription;
use AppBundle\Event\Model\TicketOffer;
use AppBundle\Form\BooleanType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Event\Enum\DistanceTransport;
use AppBundle\Event\Enum\InscriptionEtat;
use AppBundle\Event\Enum\ModeTransport;
use AppBundle\Event\Enum\InscriptionFacturation;

class TicketAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $event = $options['event'];
        $ticketOffers = $options['offers'];
        usort($ticketOffers, static fn(TicketOffer $a, TicketOffer $b): int => $a->name > $b->name ? 1 : -1);

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
                'choice_label' => fn(TicketOffer $offer): string => sprintf('%s - [%d €]', $offer->name, $offer->price),
                'choice_value' => fn(?TicketOffer $offer): ?int => $offer->ticketTypeId ?? null,
                'group_by' => fn(TicketOffer $choice): string => $choice->event ? 'Offre de l\'évènement' : 'Offre global',
            ])
            ->add('status', EnumType::class, [
                'label' => "État de l'inscription",
                'class' => InscriptionEtat::class,
                'choice_label' => static fn(InscriptionEtat $etat): string => $etat->label(),
                'required' => true,
            ])
            ->add('invoiceStatus', EnumType::class, [
                'label' => 'État de la facturation',
                'class' => InscriptionFacturation::class,
                'choice_label' => static fn(InscriptionFacturation $facturation): string => $facturation->label(),
                'required' => true,
            ]);

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
            'data_class' => Inscription::class,
            'event' => Event::class,
            'offers' => TicketOffer::class . '[]',
        ]);
    }
}
