<?php

namespace App\RendezVous\Admin\EditAttendee;

use App\RendezVous\RendezVousAttendee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditAttendeeFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('company', TextType::class, [
                'label' => 'Entreprise',
            ])
            ->add('email', TextType::class, [
                'label' => 'E-mail',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
            ])
            ->add('presence', ChoiceType::class, [
                'label' => 'Présence',
                'choices_as_values' => true,
                'choices' => [
                    '' => null,
                    'Refusé' => RendezVousAttendee::REFUSED,
                    'Vient' => RendezVousAttendee::COMING,
                    'En attente' => RendezVousAttendee::PENDING,
                ],
            ])
            ->add('confirmed', ChoiceType::class, [
                'label' => 'Confirmation',
                'choices_as_values' => true,
                'choices' => [
                    '' => null,
                    'Confirme' => RendezVousAttendee::CONFIRMED,
                    'Décline' => RendezVousAttendee::DECLINED,
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Modifier']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => EditAttendeeFormData::class]);
    }
}
