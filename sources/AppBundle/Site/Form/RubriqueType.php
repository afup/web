<?php


namespace AppBundle\Site\Form;

use AppBundle\Site\Model\Rubrique;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Afup\Site\Corporate\Feuilles;
use Afup\Site\Utils\Base_De_Donnees;
use AppBundle\Site\Model\Repository\RubriqueRepository;


class RubriqueType extends AbstractType
{

    private $rubriqueRepository;

    public function __construct(RubriqueRepository $rubriqueRepository) {
        $this->rubriqueRepository = $rubriqueRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $feuilles = new Feuilles($GLOBALS['AFUP_DB']);
        $feuillesChoices = $feuilles->obtenirListe('nom, id', 'nom', true);
        $rubriquesChoices = $this->rubriqueRepository->getAllRubriques( 'nom, id', 'nom', 'desc',  null, true);

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
                'required' => false
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

            ->add('auteur', TextType::class, [
                'required' => false,
                'label' => 'Auteur'
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
                'label' => 'position',
                'choices' => range (-9, 9)
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
