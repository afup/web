<?php

declare(strict_types=1);

namespace AppBundle\Association\Form;

use Afup\Site\Utils\Pays;
use AppBundle\Association\Entity\CompanyMember;
use AppBundle\Association\Enum\CompanyMemberStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyEditType extends AbstractType
{
    public function __construct(private readonly Pays $pays) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyName', TextType::class, [
                'label' => 'Raison sociale',
                'required' => true,
                'empty_data' => '',
                'attr' => [
                    'size' => 30,
                    'maxlength' => 40,
                ],
            ])
            ->add('siret', TextType::class, [
                'label' => 'SIRET',
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'size' => 30,
                    'maxlength' => 40,
                ],
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Adresse',
                'required' => true,
                'empty_data' => '',
                'attr' => [
                    'cols' => 42,
                    'rows' => 10,
                ],
            ])
            ->add('zipCode', TextType::class, [
                'label' => 'Code postal',
                'required' => true,
                'empty_data' => '',
                'attr' => [
                    'size' => 6,
                    'maxlength' => 10,
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'required' => true,
                'empty_data' => '',
                'attr' => [
                    'size' => 30,
                    'maxlength' => 50,
                ],
            ])
            ->add('country', ChoiceType::class, [
                'label' => 'Pays',
                'required' => true,
                'choices' => array_flip($this->pays->obtenirPays()),
            ])
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'empty_data' => '',
                'attr' => [
                    'size' => 30,
                    'maxlength' => 40,
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'required' => true,
                'empty_data' => '',
                'attr' => [
                    'size' => 30,
                    'maxlength' => 40,
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => true,
                'empty_data' => '',
                'attr' => [
                    'size' => 30,
                    'maxlength' => 100,
                ],
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
            ->add('status', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'Non finalisé' => CompanyMemberStatus::Pending,
                    'Actif' => CompanyMemberStatus::Active,
                    'Inactif' => CompanyMemberStatus::Inactive,
                ],
            ])
            ->add('maxMembers', ChoiceType::class, [
                'label' => 'Membres max',
                'choices' => array_combine($maxMembers = range(3, 18, 3), $maxMembers),
            ])
            ->add('save', SubmitType::class, ['label' => 'Soumettre']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CompanyMember::class,
        ]);
    }
}
