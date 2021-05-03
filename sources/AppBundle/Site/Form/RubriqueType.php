<?php

namespace AppBundle\Site\Form;

use Afup\Site\Corporate\Feuilles;
use AppBundle\Site\Model\Rubrique;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Site\Model\Repository\RubriqueRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RubriqueType extends AbstractType 
{
    const POSITIONS_RUBRIQUES = 9;

    private $rubriqueRepository;
    private $userRepository;

    public function __construct(RubriqueRepository $rubriqueRepository, UserRepository $userRepository)
    {
        $this->rubriqueRepository = $rubriqueRepository;
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
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
                    'size' => 60
                ]
            ])

            ->add('descriptif', TextareaType::class, [
                'label' => 'Descriptif',
                'required' => false,
                'attr' => [
                    'cols' => 42,
                    'rows' => 10,
                    'class' => 'tinymce'
                ]
            ])

            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu',
                'required' => true,
                'attr' => [
                    'cols' => 42,
                    'rows' => 20,
                    'class' => 'tinymce'
                ]
            ])

            ->add('icone', FileType::class,[
                'label' => 'Icône (Taille requise : 43 x 37 pixels)',
                'required' => false,
                'data_class' => null
            ])

            ->add('raccourci', TextType::class,[
                'required' => true,
                'label' => 'Raccourci',
                'attr' => [
                    'maxlength' => 255,
                    'size' => 60
                ]
            ])

            ->add('idParent', ChoiceType::class, [
                'label' => 'Parent',
                'choices' => $rubriques,
                'required' => false,
            ])

            ->add('idPersonnePhysique', ChoiceType::class, [
                'required' => false,
                'label' => 'Auteur',
                'choices' => $users
            ])

            ->add('date', DateType::class,[
                'required' => false,
                'label' => 'Date',
                'input'=>'datetime',
                'data' => new \Datetime(),
                'years' => range(2001,date('Y')),
                'attr' => [
                    'style' => 'display: flex;',
                ]
            ])

            ->add('position', ChoiceType::class, [
                'required' => false,
                'label' => 'Position ',
                'choices' => $positions
            ])

            ->add('pagination', IntegerType::class, [
                'required' => false
            ])

            ->add('etat', ChoiceType::class, [
                'label' => 'Etat',
                'required' => false,
                'choices' => ['Hors ligne' => -1, 'En attente' => 0, 'En ligne' => 1]
            ])

            ->add('feuilleAssociee', ChoiceType::class, [
                'label' => 'Feuille associée',
                'required' => false,
                'choices' => $feuilles
            ])
        ;
    }

}
