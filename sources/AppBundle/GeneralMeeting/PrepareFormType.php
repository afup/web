<?php

namespace AppBundle\GeneralMeeting;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;

class PrepareFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (false === $options['without_date']) {
            $builder
                ->add('date', DateType::class, [
                    'label' => 'Date de l\'AG',
                    'constraints' => [new Date()],
                    'required' => true,
                ]);
        }

        $builder
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'rows' => 5,
                    'cols' => 50,
                    'class' => 'simplemde',
                ],
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Preparer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefault('without_date', false);
    }
}
