<?php

namespace AppBundle\Association\Form;

use AppBundle\Offices\OfficesCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Url;

class CompanyPublicProfile extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $officesCollection = new OfficesCollection();

        $officesInfos = [];
        foreach ($officesCollection->getAll() as $code => $office) {
            $officesInfos[$office['label']] = $code;
        }

        $logoConstraints = [
            new File([
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                ],
            ]),
            new Image([
                'maxHeight' => 1000,
                'maxWidth' => 1000,
                'minHeight' => 200,
                'minWidth' => 200,
            ])
        ];

        if ($options['logo_required']) {
            $logoConstraints[] = new NotNull();
        }

        $builder
            ->add(
                'enabled',
                CheckboxType::class,
                [
                    'label' => 'Page publique activée',
                    'required' => false,
                    'constraints' => [
                        new Type(['type' => 'boolean']),
                    ]
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => "Description",
                    'required' => true,
                    'attr' => ['rows' => 5],
                    'help' => "Maximum 550 caractères",
                    'constraints' => [
                        new NotNull(),
                        new Length(['max' => '500']),
                    ]
                ]
            )
            ->add(
                'logo',
                FileType::class,
                [
                    'label' => "Logo",
                    'required' => $options['logo_required'],
                    'help' => 'Entre 200px x 200px et 1000px x 1000px, format JPEG ou PNG',
                    'constraints' => $logoConstraints,
                ]
            )
            ->add(
                'website_url',
                TextType::class,
                [
                    'label' => 'URL de votre site',
                    'required' => false,
                    'constraints' => [
                        new Url(),
                    ],
                ]
            )
            ->add(
                'careers_page_url',
                TextType::class,
                [
                    'label' => "URL de votre page emploi",
                    'required' => false,
                    'constraints' => [
                        new Url(),
                    ],
                ])
            ->add(
                'contact_page_url',
                TextType::class,
                [
                    'label' => "URL de votre page de contact",
                    'required' => false,
                    'constraints' => [
                        new Url(),
                    ],
                ])
            ->add(
                'twitter_handle',
                TextType::class,
                [
                    'label' => "Compte twitter",
                    'required' => false,
                ]
            )
            ->add('related_afup_offices',
                ChoiceType::class,
                [
                    'choices' => $officesInfos,
                    'multiple' => true,
                    'required' => false,
                    'label' => "Présence dans ces antennes AFUP",
                    'constraints' => [
                        new Choice([
                            'choices' => array_values($officesInfos),
                            'multiple' => true,
                        ]),
                    ]
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'logo_required' => true,
        ]);
    }
}
