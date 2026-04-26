<?php

declare(strict_types=1);

namespace AppBundle\Site\Form;

use AppBundle\Site\Entity\Repository\FeuilleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class FeuilleType extends AbstractType
{
    public const POSITIONS_RUBRIQUES = 9;

    public function __construct(private readonly FeuilleRepository $feuilleRepository) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $feuilles = [];
        foreach ($this->feuilleRepository->findAll() as $feuille) {
            $feuilles[$feuille->nom] = $feuille->id;
        }
        ksort($feuilles, SORT_NATURAL);

        $positions = [];
        for ($i = self::POSITIONS_RUBRIQUES; $i >= -(self::POSITIONS_RUBRIQUES); $i--) {
            $positions[$i] = $i;
        }

        $builder
            ->add('idParent', ChoiceType::class, [
                'label' => 'Parent',
                'choices' => $feuilles,
                'required' => false,
                'constraints' => [
                    new Assert\Type("integer"),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'maxlength' => 255,
                    'size' => 60,
                ],
                'constraints' => [
                    new Assert\Length(max: 255),
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ],
            ])
            ->add('lien', TextType::class, [
                'required' => true,
                'label' => 'Lien',
                'attr' => [
                    'maxlength' => 255,
                    'size' => 60,
                ],
                'constraints' => [
                    new Assert\Length(max: 255),
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ],
            ])
            ->add('alt', TextType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                    'size' => 60,
                ],
                'constraints' => [
                    new Assert\Length(max: 255),
                    new Assert\Type('string'),
                ],
            ])
            ->add('image', FileType::class, [
                'label' => 'Image',
                'required' => false,
                'data_class' => null,
                'mapped' => false,
                'constraints' => [
                    new Assert\Image(mimeTypes: ['image/jpg', 'image/jpeg', 'image/gif', 'image/png']),
                ],
            ])
            ->add('imageAlt', TextType::class, [
                'required' => false,
                'label' => 'Texte alternatif de l\'image',
                'attr' => [
                    'maxlength' => 255,
                    'size' => 60,
                ],
                'constraints' => [
                    new Assert\Length(max: 255),
                    new Assert\Type('string'),
                ],
            ])
            ->add('dateCreation', DateType::class, [
                'required' => false,
                'label' => 'Date',
                'input' => 'datetime',
                'years' => range(2001, date('Y')),
                'constraints' => [
                    new Assert\Type("datetime"),
                ],
            ])
            ->add('dateDebutPublication', DateType::class, [
                'required' => false,
                'label' => 'Date de début de publication',
                'input' => 'datetime',
                'years' => range(2001, date('Y') + 5),
                'constraints' => [
                    new Assert\Type("datetime"),
                ],
            ])
            ->add('dateFinPublication', DateType::class, [
                'required' => false,
                'label' => 'Date de fin de publication',
                'input' => 'datetime',
                'years' => range(2001, date('Y') + 5),
                'constraints' => [
                    new Assert\Type("datetime"),
                ],
            ])
            ->add('position', ChoiceType::class, [
                'required' => false,
                'label' => 'Position',
                'choices' => $positions,
                'constraints' => [
                    new Assert\Type("integer"),
                ],
            ])
            ->add('etat', ChoiceType::class, [
                'label' => 'Etat',
                'required' => false,
                'choices' => [
                    'Hors ligne' => -1,
                    'En attente' => 0,
                    'En ligne' => 1,
                ],
                'constraints' => [
                    new Assert\Type("integer"),
                ],
            ])
            ->add('patterns', TextareaType::class, [
                'label' => 'Patterns URL',
                'required' => false,
                'constraints' => [
                    new Assert\Type('string'),
                ],
            ])
        ;
    }
}
