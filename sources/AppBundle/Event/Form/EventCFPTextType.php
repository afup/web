<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class EventCFPTextType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fr', TextareaType::class, [
                'label' => 'CFP (fr)',
                'attr' => ['rows' => 5, 'cols' => 50, 'class' => 'simplemde'],
                'required' => false,
            ])
            ->add('en', TextareaType::class, [
                'label' => 'CFP (en)',
                'attr' => ['rows' => 5, 'cols' => 50, 'class' => 'simplemde'],
                'required' => false,
            ])
            ->add('speaker_management_fr', TextareaType::class, [
                'label' => 'Infos speakers (fr)',
                'attr' => ['rows' => 5, 'cols' => 50, 'class' => 'simplemde'],
                'required' => false,
            ])
            ->add('speaker_management_en', TextareaType::class, [
                'label' => 'Infos speakers (en)',
                'attr' => ['rows' => 5, 'cols' => 50, 'class' => 'simplemde'],
                'required' => false,
            ])
            ->add('sponsor_management_fr', TextareaType::class, [
                'label' => 'Infos sponsors (fr)',
                'attr' => ['rows' => 5,'cols' => 50, 'class' => 'simplemde'],
                'required' => false,
            ])
            ->add('sponsor_management_en', TextareaType::class, [
                'label' => 'Infos sponsors (en)',
                'attr' => ['rows' => 5, 'cols' => 50, 'class' => 'simplemde'],
                'required' => false,
            ])
            ->add('mail_inscription_content', TextareaType::class, [
                'label' => 'Contenu mail inscription',
                'attr' => ['rows' => 5, 'cols' => 50, 'class' => 'simplemde'],
                'required' => false,
            ])
            ->add('become_sponsor_description', TextareaType::class, [
                'label' => 'Contenu page devenir sponsor',
                'attr' => ['rows' => 5, 'cols' => 50, 'class' => 'simplemde'],
                'required' => false,
            ]);
    }
}
