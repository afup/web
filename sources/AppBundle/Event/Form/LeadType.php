<?php

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Lead;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LeadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('position', TextType::class)
            ->add('phone', TextType::class)
            ->add('company', TextType::class)
            ->add('website', UrlType::class, ['required' => false])
            ->add('email', EmailType::class)
            ->add('language', ChoiceType::class, [
                'choices' => ['fr' => 'fr', 'en' => 'fr'],
                'multiple' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lead::class
        ]);
    }
}
