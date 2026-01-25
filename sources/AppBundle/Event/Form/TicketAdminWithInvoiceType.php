<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\TicketOffer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketAdminWithInvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ticket', TicketAdminType::class, [
                'label' => 'Ticket',
                'event' => $options['event'],
                'offers' => $options['offers'],
                'data' => $options['data']['ticket'] ?? null,
            ])
            ->add('invoice', TicketInvoiceType::class, [
                'label' => 'Facture',
                'data' => $options['data']['invoice'] ?? null,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'event' => Event::class,
            'offers' => TicketOffer::class . '[]',
        ]);
    }
}
