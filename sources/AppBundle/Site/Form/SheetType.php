<?php

declare(strict_types=1);

namespace AppBundle\Site\Form;

use AppBundle\Site\Model\Repository\SheetRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class SheetType extends AbstractType
{
    public const POSITIONS_RUBRIQUES = 9;

    public function __construct(
        private readonly SheetRepository $sheetRepository,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $sheets = [];
        foreach ($this->sheetRepository->getAll() as $sheet) {
            $sheets[$sheet->getName()] = $sheet->getId();
        }
        ksort($sheets, SORT_NATURAL);

        $positions = [];
        for ($i = self::POSITIONS_RUBRIQUES ; $i >= -(self::POSITIONS_RUBRIQUES); $i--) {
            $positions[$i] = $i;
        }

        $builder
            ->add('idParent', ChoiceType::class, [
                'label' => 'Parent',
                'choices' => $sheets,
                'required' => false,
                'constraints' => [
                    new Assert\Type("integer"),
                ],
            ])
            ->add('name', TextType::class, [
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
            ->add('link', TextType::class, [
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
                    new Assert\Image(mimeTypes: ['image/jpg','image/jpeg','image/gif','image/png']),
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
            ->add('creationDate', DateType::class, [
                'required' => false,
                'label' => 'Date',
                'input' => 'datetime',
                'years' => range(2001, date('Y')),
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
            ->add('state', ChoiceType::class, [
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
