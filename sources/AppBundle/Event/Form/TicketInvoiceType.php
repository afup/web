<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketInvoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $paymentStatuses = [
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

        $paymentTypes = [
            'Carte bancaire' => Ticket::PAYMENT_CREDIT_CARD,
            'Chèque' => Ticket::PAYMENT_CHEQUE,
            'Virement' => Ticket::PAYMENT_BANKWIRE,
            'Aucun' => Ticket::PAYMENT_NONE,
        ];

        $builder
            ->add('reference', TextType::class, [
                'label' => 'Référence',
                'disabled' => !$options['data'],
            ])
            ->add('authorization', TextType::class, [
                'label' => 'Autorisation',
            ])
            ->add('transaction', TextType::class, [
                'label' => 'Transaction',
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'État du paiement',
                'choices' => $paymentStatuses,
            ])
            ->add('invoice', ChoiceType::class, [
                'label' => 'État de la facturation',
                'choices' => $invoiceStatuses,
                'mapped' => false,
            ])

            ->add('paymentType', ChoiceType::class, [
                'label' => 'Règlement',
                'expanded' => true,
                'row_attr' => ['class' => 'fields inline'],
                'choices' => $paymentTypes,
            ])
            ->add('paymentInfos', TextareaType::class, [
                'label' => 'Informations règlement',
                'required' => false,
            ])
            ->add('paymentDate', DateType::class, [
                'label' => 'Date',
            ])

            ->add('company', TextType::class, [
                'label' => 'Société',
            ])
            ->add('company', TextType::class, [
                'label' => 'Société',
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Adresse',
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Code postal',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
        ]);
    }
}
