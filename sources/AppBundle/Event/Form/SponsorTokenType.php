<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Entity\SponsorTicket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SponsorTokenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('societe', TextType::class, [
                'label' => 'Sponsor (société)',
                'empty_data' => '',
            ])
            ->add('contactEmail', EmailType::class, [
                'label' => 'Email de contact',
                'empty_data' => '',
            ])
            ->add('token', TextType::class, ['empty_data' => ''])
            ->add('maxInvitations', IntegerType::class, [
                'label' => 'Nombre d\'invitations',
                'empty_data' => '0',
            ])
            ->add('qrCodesScannerAvailable', CheckboxType::class, [
                'label' => 'Autoriser le scan de QR Codes',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SponsorTicket::class,
        ]);
    }
}
