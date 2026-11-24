<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Association\Genre;
use AppBundle\Event\Entity\Inscription;
use AppBundle\Event\Enum\DistanceTransport;
use AppBundle\Event\Enum\ModeTransport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class SponsorTicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
            ]);

        if ($options['with_transport']) {
            $builder
                ->add('transport_mode', EnumType::class, [
                    'property_path' => 'transportMode',
                    'label' => 'Votre mode de transport ?',
                    'placeholder' => '',
                    'required' => true,
                    'constraints' => [new NotBlank()],
                    'class' => ModeTransport::class,
                    'choices' => ModeTransport::choixParLibelle(),
                    'choice_label' => static fn(ModeTransport $mode): string => $mode->label(),
                ])
                ->add('transport_distance', EnumType::class, [
                    'property_path' => 'transportDistance',
                    'label' => 'La distance parcourue ?',
                    'placeholder' => '',
                    'required' => true,
                    'constraints' => [new NotBlank()],
                    'class' => DistanceTransport::class,
                    'choice_label' => static fn(DistanceTransport $distance): string => $distance->label(),
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inscription::class,
            'with_transport' => false,
        ]);
    }
}
