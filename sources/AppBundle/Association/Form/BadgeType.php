<?php

declare(strict_types=1);

namespace AppBundle\Association\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class BadgeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'label',
                TextType::class,
                [
                    'label' => 'Nom',
                ]
            )
            ->add(
                'image',
                FileType::class,
                [
                    'label' => 'Image',
                    'constraints' => [
                        new Image([
                            'minWidth' => 850,
                            'maxWidth' => 850,
                            'minHeight' => 850,
                            'maxHeight' => 850,
                            'mimeTypes' => [
                                'image/png',
                            ],
                        ]),
                    ],
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Créer',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
