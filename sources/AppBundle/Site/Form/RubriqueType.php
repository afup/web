<?php

declare(strict_types=1);

namespace AppBundle\Site\Form;

use Afup\Site\Corporate\Feuilles;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class RubriqueType extends AbstractType
{
    const POSITIONS_RUBRIQUES = 9;

    public function __construct(
        private readonly RubriqueRepository $rubriqueRepository,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $users = [];
        foreach ($this->userRepository->getAll() as $user) {
            $users[$user->getLastName() . ' ' . $user->getFirstName()] = $user->getId();
        }
        $feuilles = (new Feuilles($GLOBALS['AFUP_DB']))->obtenirListe('nom, id', 'nom', true);
        $positions = [];
        for ($i = self::POSITIONS_RUBRIQUES ; $i >= -(self::POSITIONS_RUBRIQUES); $i--) {
            $positions[$i] = $i;
        }
        $rubriques = [];
        foreach ($this->rubriqueRepository->getAll() as $rubrique) {
            $rubriques[$rubrique->getNom()] = $rubrique->getId();
        }
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la rubrique',
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
            ->add('descriptif', TextareaType::class, [
                'label' => 'Descriptif',
                'required' => false,
                'attr' => [
                    'maxlength' => 255,
                    'cols' => 42,
                    'rows' => 10,
                    'class' => 'tinymce',
                ],
                'constraints' => [
                    new Assert\Length(['max' => 255]),
                    new Assert\Type('string'),
                ],
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu',
                'required' => true,
                'attr' => [
                    'cols' => 42,
                    'rows' => 20,
                    'class' => 'tinymce',
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                    new Assert\NotBlank(),
                ],
            ])
            ->add('icone', FileType::class, [
                'label' => 'Icône (Taille requise : 43 x 37 pixels)',
                'required' => false,
                'data_class' => null,
                'constraints' => [
                    new Assert\Image([
                        'minHeight' => 37,
                        'maxHeight' => 43,
                    ]),
                ],
            ])
            ->add('raccourci', TextType::class, [
                'required' => true,
                'label' => 'Raccourci',
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
                'choices' => $rubriques,
                'required' => false,
                'constraints' => [
                    new Assert\Type("integer"),
                ],
            ])
            ->add('idPersonnePhysique', ChoiceType::class, [
                'required' => false,
                'label' => 'Auteur',
                'choices' => $users,
                'constraints' => [
                    new Assert\Type("integer"),
                ],
            ])
            ->add('date', DateType::class, [
                'required' => false,
                'label' => 'Date',
                'input'=>'datetime',
                'years' => range(2001, date('Y')),
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
            ->add('pagination', IntegerType::class, [
                'required' => false,
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
            ->add('feuilleAssociee', ChoiceType::class, [
                'label' => 'Feuille associée',
                'required' => false,
                'choices' => $feuilles,
                'constraints' => [
                    new Assert\Type("integer"),
                ],
            ])
        ;
    }
}
