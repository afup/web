<?php

declare(strict_types=1);

namespace AppBundle\SpeakerInfos\Form;

use AppBundle\Event\Speaker\MicrophoneType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpeakersMicrophoneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', EnumType::class, [
                'class'                     => MicrophoneType::class,
                'label'                     => 'speaker_infos.microphone.label',
                'expanded'                  => true,
                'placeholder'               => 'speaker_infos.microphone.placeholder',
                'choice_label'              => fn(MicrophoneType $type) => match ($type) {
                    MicrophoneType::Headset  => 'speaker_infos.microphone.choice.headset',
                    MicrophoneType::Handheld => 'speaker_infos.microphone.choice.handheld',
                },
                'choice_translation_domain' => true,
                'required'                  => false,
            ])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['csrf_protection' => false]);
    }
}
