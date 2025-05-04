<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SponsorTicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
            ]);

        if ($options['with_transport']) {
            $transportMode = Ticket::TRANSPORT_MODES;
            asort($transportMode);

            $builder
                ->add('transport_mode', ChoiceType::class, [
                    'label' => 'Votre mode de transport ?',
                    'placeholder' => '',
                    'required' => true,
                    'constraints' => [new NotBlank()],
                    'choices' => array_flip($transportMode),
                ])
                ->add('transport_distance', ChoiceType::class, [
                    'label' => 'La distance parcourue ?',
                    'placeholder' => '',
                    'required' => true,
                    'constraints' => [new NotBlank()],
                    'choices' => array_flip(Ticket::TRANSPORT_DISTANCES),
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
            'with_transport' => false,
        ]);
    }
}
