<?php

declare(strict_types=1);

namespace AppBundle\SpeakerInfos\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\All;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\File;

class SpeakersExpensesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'files',
                FileType::class,
                [
                    'label' => 'Choisissez vos fichiers',
                    'multiple' => true,
                    'data_class' => null,
                    'constraints' => [
                        new Count(['max' => 4]),
                        new All([
                            new File([
                                'maxSize' => '2M',
                                'mimeTypes' => [
                                    'application/pdf',
                                    'application/x-pdf',
                                ],
                            ]),
                        ]),
                    ],
                    'attr' => [
                        'accept' => '.pdf',
                    ],
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Ajouter des fichiers'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }
}
