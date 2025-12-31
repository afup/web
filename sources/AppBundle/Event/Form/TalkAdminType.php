<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Repository\SpeakerRepository;
use AppBundle\Event\Model\Speaker;
use AppBundle\Event\Model\Talk;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TalkAdminType extends TalkType
{
    public function __construct(private readonly SpeakerRepository $speakerRepository) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $allSpeakers = $this->speakerRepository->searchSpeakers($options['event']);
        $speakers = $this->speakerRepository->getSpeakersByTalk($builder->getData());
        $speakers = iterator_to_array($speakers->getIterator());

        $builder->remove('hasAllowedToSharingWithLocalOffices');
        $builder
            ->add('hasAllowedToSharingWithLocalOffices', CheckboxType::class, [
                'label' => 'Autoriser l’AFUP à transmettre ma proposition de conférence à ses antennes locales ?',
                'required' => false,
            ])
            ->add('joindinId', TextType::class, [
                'label' => 'joind.in ID',
                'required' => false,
                'attr' => ['placeholder' => '4639e'],
            ])
            ->add('youtubeId', TextType::class, [
                'label' => 'Youtube ID',
                'required' => false,
                'attr' => ['placeholder' => '9P7K3sdg6s4'],
            ])
            ->add('slidesUrl', UrlType::class, [
                'label' => 'URL des slides',
                'required' => false,
                'default_protocol' => 'https',
                'attr' => ['placeholder' => 'https://'],
            ])
            ->add('openfeedbackPath', TextType::class, [
                'label' => 'Open Feedback (path)',
                'required' => false,
                'attr' => ['placeholder' => 'forumphp2025/2025-10-09/5394'],
            ])
            ->add('blogPostUrl', UrlType::class, [
                'label' => 'URL du blog',
                'required' => false,
                'default_protocol' => 'https',
                'attr' => ['placeholder' => 'https://'],
            ])
            ->add('interviewUrl', UrlType::class, [
                'label' => 'URL de l\'interview',
                'required' => false,
                'default_protocol' => 'https',
                'attr' => ['placeholder' => 'https://'],
            ])
            ->add('languageCode', ChoiceType::class, [
                'label' => 'Langue',
                'required' => false,
                'choices' => array_flip(Talk::getLanguageLabelsByKey()),
            ])
            ->add('tweets', TextareaType::class, [
                'label' => 'Tweets',
                'required' => false,
            ])
            ->add('submittedOn', DateTimeType::class, [
                'label' => 'Date de soumission',
            ])
            ->add('publishedOn', DateTimeType::class, [
                'label' => 'Date de publication',
                'required' => false,
            ])
            ->add('speakers', ChoiceType::class, [
                'mapped' => false,
                'multiple' => true,
                'choices' => $allSpeakers,
                'required' => false,
                'data' => $speakers,
                'choice_label' => fn(Speaker $speaker) => $speaker->getLabel(),
                'choice_value' => fn(?Speaker $speaker) => $speaker?->getId(),
                'choice_name' => 'id',
            ])
            ->add('verbatim', TextareaType::class, [
                'label' => 'Verbatim',
                'required' => false,
            ])
            ->add('save', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Talk::class);
        $resolver->setDefault('event', EventType::class);

        parent::configureOptions($resolver);
    }
}
