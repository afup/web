<?php

declare(strict_types=1);


namespace AppBundle\Event\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class SpeakerSuggestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'suggester_email',
                TextType::class,
                [
                    'label' => 'Votre email',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['max' => 254]),
                        new Email(['checkMX' => true]),
                    ],
                ]
            )
            ->add(
                'suggester_name',
                TextType::class,
                [
                    'label' => 'Vos nom/prénom',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['max' => 254]),
                    ],
                ]
            )
            ->add(
                'speaker_name',
                TextType::class,
                    [
                        'label' => 'Conférencier·e suggéré·e',
                        'constraints' => [
                            new NotBlank(),
                            new Length(['max' => 254]),
                        ],
                    ]
            )
            ->add(
                'comment',
                TextareaType::class,
                [
                    'required' => false,
                    'label' => 'Commentaire',
                    'constraints' => [
                        new NotBlank(),
                        new Length(['max' => 1500]),
                    ],
                ]
            )
            ->add('save', SubmitType::class, ['label' => 'Suggérer'])
        ;
    }
}
