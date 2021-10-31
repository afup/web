<?php

namespace AppBundle\GeneralMeeting;

use AppBundle\Association\Model\GeneralMeetingQuestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class GeneralMeetingQuestionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label', TextareaType::class, [
                'label' => 'Question',
                'attr' => [
                    'rows' => 5,
                    'cols' => 50,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length(['min' => 5, 'max' => 255])
                ]
            ])
            ->add('submit', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GeneralMeetingQuestion::class,
        ]);
    }

}
