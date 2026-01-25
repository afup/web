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
                'required' => false,
            ])
            ->add('authorization', TextType::class, [
                'label' => 'Autorisation',
                'required' => false,
            ])
            ->add('transaction', TextType::class, [
                'label' => 'Transaction',
                'required' => false,
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
                'required' => false,
            ])

            ->add('company', TextType::class, [
                'label' => 'Société',
                'required' => false,
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
