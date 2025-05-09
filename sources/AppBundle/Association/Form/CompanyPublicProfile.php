<?php

declare(strict_types=1);

namespace AppBundle\Association\Form;

use AppBundle\Antennes\AntennesCollection;
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
    const DESCRIPTION_MAX_LENGTH = 2000;
    const MEMBERSHIP_REASON_MAX_LENGTH = 150;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $antennesCollection = new AntennesCollection();

        $antennesInfos = [];
        foreach ($antennesCollection->getAll() as $antenne) {
            $antennesInfos[$antenne->label] = $antenne->code;
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
            ]),
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
                    ],
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => "Description",
                    'required' => true,
                    'attr' => ['rows' => 5],
                    'help' => sprintf("Maximum %s caractères", self::DESCRIPTION_MAX_LENGTH),
                    'constraints' => [
                        new NotNull(),
                        new Length(['max' => self::DESCRIPTION_MAX_LENGTH]),
                    ],
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
                    'help' => 'Exemple "@afup"',
                ]
            )
            ->add('related_afup_offices',
                ChoiceType::class,
                [
                    'choices' => $antennesInfos,
                    'multiple' => true,
                    'required' => false,
                    'label' => "Présence dans ces antennes AFUP",
                    'constraints' => [
                        new Choice([
                            'choices' => array_values($antennesInfos),
                            'multiple' => true,
                            'strict' => true,
                        ]),
                    ],
                ]
            )
            ->add(
                'membership_reason',
                TextType::class,
                [
                    'label' => "Pourquoi êtes-vous membre ?",
                    'help' => sprintf($this->getMembershipReasonHelp(), self::MEMBERSHIP_REASON_MAX_LENGTH),
                    'required' => false,
                    'constraints' => [
                        new Length(['max' => self::MEMBERSHIP_REASON_MAX_LENGTH]),
                    ],
                ])
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'logo_required' => true,
        ]);
    }

    private function getMembershipReasonHelp(): string
    {
        return <<<EOF
Décrivez en moins de %s caractères pourquoi vous êtes membre AFUP.
Votre réponse sera utilisée sur votre profil public ainsi que sur les différentes communications de l'association.
EOF;
    }
}
