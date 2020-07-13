<?php

namespace AppBundle\Event\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventCFPTextType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cfp_fr', TextareaType::class, ['required' => false, 'label' => 'CFP (fr)'])
            ->add('cfp_en', TextareaType::class, ['required' => false, 'label' => 'CFP (en)'])
            ->add('speaker_management_fr', TextareaType::class, ['required' => false, 'label' => 'Infos speakers (fr)'])
            ->add('speaker_management_en', TextareaType::class, ['required' => false, 'label' => 'Infos speakers (en)'])
            ->add('sponsor_management_fr', TextareaType::class, ['required' => false, 'label' => 'Infos sponsors (fr)'])
            ->add('sponsor_management_en', TextareaType::class, ['required' => false, 'label' => 'Infos sponsors (en)'])
            ->add('mail_inscription_content', TextareaType::class,
                ['required' => false, 'label' => 'Contenu mail inscription'])
            ->add('become_sponsor_description', TextareaType::class,
                ['required' => false, 'label' => 'Contenu page devenir sponsor']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
