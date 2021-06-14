<?php

namespace AppBundle\Site\Form;

use AppBundle\Site\Model\Repository\FeuilleRepository;
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
    const POSITIONS_FEUILLES = 9;

    private $feuilleRepository;

    public function __construct(FeuilleRepository $feuilleRepository)
    {
        $this->feuilleRepository = $feuilleRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $positions = [];
        for ($i = self::POSITIONS_FEUILLES ; $i >= -(self::POSITIONS_FEUILLES); $i--) {
            $positions[$i] = $i;
        }

        $feuilles = [];
        foreach ($this->feuilleRepository->getAll() as $feuille) {
            $feuilles[$feuille->getNom()] = $feuille->getId();
        }
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la feuille',
                'required' => true,
                'attr' => [
                    'maxlength' => 255,
                    'size' => 60,
                ],
                'constraints' => [
                    new Assert\Length(['max' => 255]),
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ],
            ])

            ->add('idParent', ChoiceType::class, [
                'label' => 'Parent',
                'choices' => $feuilles,
                'required' => false,
                'constraints' => [
                    new Assert\Type("integer"),
                ],
            ])

            ->add('lien', TextType::class, [
                'label' => 'Lien',
                'required' => true,
                'attr' => [
                    'maxlength' => 255,
                ],
                'constraints' => [
                    new Assert\Length(['max' => 255]),
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ],
            ])

            ->add('alt', TextType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                ],
                'constraints' => [
                    new Assert\Length(['max' => 255]),
                ],
            ])

            ->add('image', FileType::class,[
                'label' => 'Image',
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new Assert\Image(),
                ]
            ])

            ->add('date', DateType::class,[
                'required' => false,
                'label' => 'Date',
                'input'=>'datetime',
                'years' => range(2001,date('Y')),
                'attr' => [
                    'style' => 'display: flex;',
                ],
                'constraints' => [
                    new Assert\Type("datetime"),
                ],
            ])

            ->add('position', ChoiceType::class, [
                'required' => false,
                'label' => 'Position ',
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

            ->add('patterns', TextAreaType::class,[
                'required' => false,
                'label' => 'Patterns URL',
                'attr' => [
                    'rows' => 6,
                ],
                'constraints' => [
                    new Assert\Type('string'),
                ],
            ])
        ;
    }
}
