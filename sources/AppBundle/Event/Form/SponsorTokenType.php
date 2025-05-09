<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\SponsorTicket;
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
            ->add('company', TextType::class, [
                'label' => 'Sponsor (société)',
            ])
            ->add('contactEmail', EmailType::class, [
                'label' => 'Email de contact',
            ])
            ->add('token', TextType::class)
            ->add('maxInvitations', IntegerType::class, [
                'label' => 'Nombre d\'invitations',
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
