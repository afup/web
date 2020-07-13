<?php

namespace AppBundle\Event\Form;


use AppBundle\Event\Model\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre de l\'événément'])
            ->add('path', TextType::class, [
                'label' => 'Chemin du template',
                'help' => 'Le path sert également à déterminer le nom du template de mail à utiliser sur mandrill, sous la forme confirmation-inscription-{PATH}'
            ])
            ->add('trelloListId', TextType::class, ['required' => false, 'label' => 'Liste trello pour les leads'])
            ->add('logoUrl', UrlType::class, ['required' => false, 'label' => 'URL du logo de l\'événement'])
            ->add('seats', NumberType::class, ['label' => 'Nombre de places'])
            ->add('placeName', TextType::class, ['required' => false, 'label' => 'Nom du lieu'])
            ->add('placeAddress', TextType::class, ['required' => false, 'label' => 'Adresse du lieu'])
            ->add('dateStart', DateType::class,
                ['required' => false, 'widget' => 'single_text', 'label' => 'Date de début'])
            ->add('dateEnd', DateType::class,
                ['required' => false, 'widget' => 'single_text', 'label' => 'Date de fin'])
            ->add('dateEndCallForProjects', DateTimeType::class,
                ['required' => false, 'widget' => 'single_text', 'label' => 'Date de fin de l\'appel aux projets'])
            ->add('dateEndCallForPapers', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date de fin de l\'appel aux conférenciers'
            ])
            ->add('voteEnabled', CheckboxType::class,
                ['required' => false, 'label' => 'Activer le vote sur les conférences'])
            ->add('dateEndVote', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date de fin de vote sur le CFP'
            ])
            ->add('dateEndPreSales', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date de fin de pré-vente'
            ])
            ->add('dateEndSales', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date de fin de vente'
            ])
            ->add('dateEndSpeakersDinerInfosCollection', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date de fin saisie repas confférenciers'
            ])
            ->add('dateEndHotelInfosCollection', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date de fin saisie nuités hotel'
            ])
            ->add('datePlanningAnnouncement', DateTimeType::class, [
                'required' => false,
                'widget' => 'single_text',
                'label' => 'Date annonce planning'
            ])
            ->add('cFP', EventCFPTextType::class, ['required'=>false, 'label'=>false])
            ->add('speakersDinerEnabled', CheckboxType::class, ['required'=>false, 'label'=>'Activer le repas des speakers'])
            ->add('accomodationEnabled', CheckboxType::class, ['required'=>false, 'label'=>'Activer les nuits d\'hôtel'])
            ->add('coupons', TextareaType::class, ['required'=>false, 'mapped'=>false, 'label'=>'Liste des coupons', 'attr' => ['placeholder'=>'Ici c\'est une liste de coupons séparées par des virgules', 'title'=>'Ici c\'est une liste de coupons séparées par des virgules']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class' => Event::class]);
    }

}
