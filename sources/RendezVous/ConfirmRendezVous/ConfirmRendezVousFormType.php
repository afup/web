<?php

namespace App\RendezVous\ConfirmRendezVous;

use App\RendezVous\RendezVousAttendee;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ConfirmRendezVousFormType extends AbstractType
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
            ->add('presence', HiddenType::class)
            ->add('confirmed', ChoiceType::class, [
                'label' => 'Confirmation',
                'choices_as_values' => true,
                'choices' => [
                    '' => null,
                    'OUI, je serai bien présent' => RendezVousAttendee::CONFIRMED,
                    'NON, je ne serai pas là finalement' => RendezVousAttendee::DECLINED,
                ],
            ])
            ->add('submit', SubmitType::class, ['label' => 'Envoyer']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('data_class', ConfirmRendezVousFormData::class);
    }
}
