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
            ->add('cfp_fr', TextareaType::class, ['required' => false, 'label' => 'CFP (fr)', 'attr'=>['rows'=> 5, 'cols'=>50, 'class'=>'simplemde']])
            ->add('cfp_en', TextareaType::class, ['required' => false, 'label' => 'CFP (en)', 'attr'=>['rows'=> 5, 'cols'=>50, 'class'=>'simplemde']])
            ->add('speaker_management_fr', TextareaType::class, ['required' => false, 'label' => 'Infos speakers (fr)', 'attr'=>['rows'=> 5, 'cols'=>50, 'class'=>'tinymce']])
            ->add('speaker_management_en', TextareaType::class, ['required' => false, 'label' => 'Infos speakers (en)', 'attr'=>['rows'=> 5, 'cols'=>50, 'class'=>'tinymce']])
            ->add('sponsor_management_fr', TextareaType::class, ['required' => false, 'label' => 'Infos sponsors (fr)', 'attr'=>['rows'=> 5, 'cols'=>50, 'class'=>'tinymce']])
            ->add('sponsor_management_en', TextareaType::class, ['required' => false, 'label' => 'Infos sponsors (en)', 'attr'=>['rows'=> 5, 'cols'=>50, 'class'=>'tinymce']])
            ->add('mail_inscription_content', TextareaType::class,
                ['required' => false, 'label' => 'Contenu mail inscription', 'attr'=>['rows'=> 5, 'cols'=>50, 'class'=>'simplemde']])
            ->add('become_sponsor_description', TextareaType::class,
                ['required' => false, 'label' => 'Contenu page devenir sponsor', 'attr'=>['rows'=> 5, 'cols'=>50, 'class'=>'simplemde']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
