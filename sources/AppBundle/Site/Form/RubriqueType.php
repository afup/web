<?php

namespace AppBundle\Site\Form;

use AppBundle\Site\Model\Repository\RubriqueRepository;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Site\Model\Rubrique;
use Afup\Site\Corporate\Feuilles;
use Afup\Site\Utils\Base_De_Donnees;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RubriqueType extends AbstractType
{
    private $rubriqueRepository;
    private $userRepository;

    public function __construct(RubriqueRepository $rubriqueRepository, UserRepository $userRepository) {
        $this->rubriqueRepository = $rubriqueRepository;
        $this->userRepository = $userRepository;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $users = [null => ''];
        foreach ($this->userRepository->search() as $user) {
            $users[$user->getLastName().' '.$user->getFirstName()] = $user->getId();
        }
        $feuilles = new Feuilles($GLOBALS['AFUP_DB']);
        $feuillesChoices = $feuilles->obtenirListe('nom, id', 'nom', true);
        $rubriquesChoices = $this->rubriqueRepository->getAllRubriques( 'nom, id', 'nom', 'desc',  null, true);
        $positions = array();
        for ($i = 9; $i >= -9; $i--) {
            $positions[$i] = $i;
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
                'label' => 'Icône',
                'required' => false,
                'help' => 'Taille requise : 43 x 37 pixels',
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

            ->add('parent', ChoiceType::class, [
                'required' => false,
                'choices' => $rubriquesChoices, 
                'label'=>'Rubrique parente'
            ])

            ->add('auteur', ChoiceType::class, [
                'required' => false,
                'label' => 'Auteur',
                'choices' => $users
            ])

            ->add('date', DateType::class,[
                'required' => false,
                'label' => 'Date',                    
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
                'choices' => array('Hors ligne' => -1, 'En attente' => 0, 'En ligne' => 1)
            ])

            ->add('feuille_associee', ChoiceType::class, [
                'label' => 'Feuille associée',
                'required' => false,
                'choices' => $feuillesChoices
            ])

            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RubriqueEditFormData::class,
        ]);
    }
}
