<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'événément',
                'constraints' => [new Assert\NotBlank(['message' => 'Titre du forum manquant'])]
            ])
            ->add('path', TextType::class, [
                'label' => 'Chemin du template',
                'help' => 'Le path sert également à déterminer le nom du template de mail à utiliser sur mandrill, sous la forme confirmation-inscription-{PATH}',
                'constraints' => [new Assert\NotBlank()]
            ])
            ->add('logoUrl', UrlType::class, [
                'label' => 'URL du logo de l\'événement',
                'required' => false,
            ])
            ->add('seats', NumberType::class, [
                'label' => 'Nombre de places',
                'constraints' => [new Assert\NotBlank(['message' => 'Nombre de places manquant'])]
            ])
            ->add('placeName', TextType::class, [
                'label' => 'Nom du lieu',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Length(['max' => 255]
                )],
            ])
            ->add('placeAddress', TextType::class, [
                'label' => 'Adresse du lieu',
                'required' => false,
            ])
            ->add('dateStart', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de début',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('dateEnd', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin',
                'constraints' => [new Assert\NotBlank()],
            ])
            ->add('dateEndCallForProjects', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin de l\'appel aux projets',
                'required' => false,
            ])
            ->add('dateEndCallForPapers', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin de l\'appel aux conférenciers',
                'required' => false,
            ])
            ->add('voteEnabled', CheckboxType::class, [
                'label' => 'Activer le vote sur les conférences',
                'required' => false,
            ])
            ->add('dateEndVote', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin de vote sur le CFP',
                'required' => false,
            ])
            ->add('dateEndPreSales', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin de pré-vente',
                'required' => false,
            ])
            ->add('dateEndSales', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin de vente',
                'required' => false,
            ])
            ->add('waitingListUrl', UrlType::class, [
                'required' => false,
                'label' => 'URL de la liste d\'attente ',
            ])
            ->add('dateEndSpeakersDinerInfosCollection', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin saisie repas conférenciers',
                'required' => false,
            ])
            ->add('dateEndHotelInfosCollection', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date de fin saisie nuitées hotel',
                'required' => false,
            ])
            ->add('datePlanningAnnouncement', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date annonce planning',
                'required' => false,
            ])
            ->add('dateEndSalesSponsorToken', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Date fin saisie token sponsor',
                'required' => false,
            ])
            ->add('CFP', EventCFPTextType::class, [
                'label' => false
            ])
            ->add('speakersDinerEnabled', CheckboxType::class, [
                'label' => 'Activer le repas des speakers',
                'required' => false,
            ])
            ->add('accomodationEnabled', CheckboxType::class, [
                'label' => 'Activer les nuits d\'hôtel',
                'required' => false,
            ])
            ->add('coupons', TextareaType::class, [
                'mapped' => false,
                'label' => 'Liste des coupons',
                'attr' => [
                    'placeholder' => 'Ici c\'est une liste de coupons séparées par des virgules',
                    'title' => 'Ici c\'est une liste de coupons séparées par des virgules'
                ],
                'required' => false,
            ])
            ->add('pricesDefinedWithVat', CheckboxType::class, [
                'label' => 'Prix définis en incluant la TVA (définis en TTC) ',
                'required' => false,
                'disabled' => true,
            ])
            ->add('registration_email_file', FileType::class, [
                'label' => ' Pièce jointe du mail d\'inscription',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Assert\File(['mimeTypes'=> 'application/pdf'])
                ]
            ])
            ->add('sponsor_file_fr', FileType::class, [
                'label' => ' Dossier de sponsoring (FR)',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Assert\File(['mimeTypes'=> 'application/pdf'])
                ]
            ])
            ->add('sponsor_file_en', FileType::class, [
                'label' => 'Dossier de sponsoring (EN)',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Assert\File(['mimeTypes'=> 'application/pdf'])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Event::class]);
    }
}
