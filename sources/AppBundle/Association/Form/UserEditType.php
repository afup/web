<?php

namespace AppBundle\Association\Form;

use Afup\Site\Association\Personnes_Morales;
use Afup\Site\Utils\Pays;
use AppBundle\Association\Model\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserEditType extends AbstractType
{
    /** @var Personnes_Morales */
    private $personnesMorales;
    /** @var Pays */
    private $pays;

    public function __construct(
        Personnes_Morales $personnesMorales,
        Pays $pays
    ) {
        $this->personnesMorales = $personnesMorales;
        $this->pays = $pays;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $civilities = ['M.', 'Mme'];
        $builder
            ->add('companyId', ChoiceType::class, [
                'label' => 'Personne morale',
                'required' => false,
                'choices' => array_flip($this->personnesMorales->obtenirListe('id, CONCAT(raison_sociale, " (id : ", id, ")")', 'raison_sociale', true)),
            ])
            ->add('civility', ChoiceType::class, [
                'label' => 'Civilité',
                'required' => true,
                'choices' => array_combine($civilities, $civilities),
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'size' => 30,
                    'maxlength' => 40,
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'attr' => [
                    'size' => 30,
                    'maxlength' => 40,
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'attr' => [
                    'size' => 30,
                    'maxlength' => 100,
                ],
            ])
            ->add('alternateEmail', EmailType::class, [
                'label' => 'Email pour comparatif slack',
                'required' => false,
                'attr' => [
                    'size' => 30,
                    'maxlength' => 100,
                ],
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Adresse',
                'required' => true,
                'attr' => [
                    'cols' => 42,
                    'rows' => 10,
                ],
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Code postal',
                'required' => true,
                'attr' => [
                    'size' => 6,
                    'maxlength' => 10,
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => true,
                'attr' => [
                    'size' => 30,
                    'maxlength' => 50,
                ],
            ])
            ->add('countryId', ChoiceType::class, [
                'label' => 'Pays',
                'required' => true,
                'choices' => array_flip($this->pays->obtenirPays()),
            ])
            ->add('phone', TextType::class, [
                'label' => 'Tél. fixe',
                'required' => false,
                'attr' => [
                    'size' => 20,
                    'maxlength' => 20,
                ],
            ])
            ->add('cellphone', TextType::class, [
                'label' => 'Tél. portable',
                'required' => false,
                'attr' => [
                    'size' => 20,
                    'maxlength' => 20,
                ],
            ])
            ->add('level', ChoiceType::class, [
                'label' => 'Niveau',
                'choices' => [
                    'Membre' => User::LEVEL_MEMBER,
                    'Rédacteur' => User::LEVEL_WRITER,
                    'Administrateur' => User::LEVEL_ADMIN,
                ],
            ])
            ->add('directoryLevel', ChoiceType::class, [
                'label' => 'Annuaire des prestataires',
                'choices' => [
                    '--' => User::LEVEL_MEMBER,
                    'Gestionnaire' => User::LEVEL_ADMIN,
                ],
            ])
            ->add('eventLevel', ChoiceType::class, [
                'label' => 'Évènement',
                'choices' => [
                    '--' => User::LEVEL_MEMBER,
                    'Gestionnaire' => User::LEVEL_ADMIN,
                ],
            ])
            ->add('websiteLevel', ChoiceType::class, [
                'label' => 'Site web',
                'choices' => [
                    '--' => User::LEVEL_MEMBER,
                    'Gestionnaire' => User::LEVEL_ADMIN,
                ],
            ])
            ->add('officeLevel', ChoiceType::class, [
                'label' => 'Antenne AFUP',
                'choices' => [
                    '--' => User::LEVEL_MEMBER,
                    'Gestionnaire' => User::LEVEL_ADMIN,
                ],
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Non finalisé' => User::STATUS_PENDING,
                    'Actif' => User::STATUS_ACTIVE,
                    'Inactif' => User::STATUS_INACTIVE,
                ],
            ])
            ->add('login', TextType::class, [
                'label' => 'Login',
                'required' => true,
                'attr' => [
                    'size' => 30,
                    'maxlength' => 30,
                ],
            ])
            ->add('password', RepeatedType::class, [
                'required' => false,
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'size' => 30,
                        'maxlength' => 30,
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmation mot de passe',
                    'attr' => [
                        'size' => 30,
                        'maxlength' => 30,
                    ],
                ],
            ])
            ->add('roles', TextareaType::class, [
                'label' => 'Rôles',
                'attr' => [
                    'cols' => 42,
                    'rows' => 5,
                ],
            ])
            ->add('needsUpToDateMembership', CheckboxType::class, [
                'label' => 'Nécessite une cotisation à jour',
                'required' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Ajouter']);
    }
}
