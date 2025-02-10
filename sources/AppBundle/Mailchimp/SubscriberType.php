<?php

declare(strict_types=1);


namespace AppBundle\Mailchimp;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class SubscriberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['attr' => ['placeholder' => 'Entrer son email pour s\'abonner'], 'label' => false])
            ->add('save', SubmitType::class, ['label' => 'OK'])
        ;
    }
}
