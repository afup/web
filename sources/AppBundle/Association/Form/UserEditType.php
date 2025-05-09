<?php

declare(strict_types=1);

namespace AppBundle\Association\Form;

use Afup\Site\Utils\Pays;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserEditType extends AbstractType
{
    public function __construct(
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly Pays $pays,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $civilities = ['M.', 'Mme'];
        $builder
            ->add('companyId', ChoiceType::class, [
                'label' => 'Personne morale',
                'required' => false,
                'choices' => array_flip($this->companyMemberRepository->getList()),
            ])
            ->add('civility', ChoiceType::class, [
                'label' => 'Civilité',
                'required' => true,
                'choices' => array_combine($civilities, $civilities),
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank(),
                ],
                'attr' => [
                    'size' => 30,
                    'maxlength' => 40,
                ],
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new NotBlank(),
                ],
                'attr' => [
                    'size' => 30,
                    'maxlength' => 40,
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'constraints' => [
                    new Email(),
                ],
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
                'constraints' => [
                    new NotBlank(),
                ],
                'attr' => [
                    'cols' => 42,
                    'rows' => 10,
                ],
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Code postal',
                'constraints' => [
                    new NotBlank(),
                ],
                'attr' => [
                    'size' => 6,
                    'maxlength' => 10,
                ],
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
                'constraints' => [
                    new NotBlank(),
                ],
                'attr' => [
                    'size' => 30,
                    'maxlength' => 50,
                ],
            ])
            ->add('country', ChoiceType::class, [
                'label' => 'Pays',
                'constraints' => [
                    new NotBlank(),
                ],
                'preferred_choices' => ['FR'],
                'choices' => array_flip($this->pays->obtenirPays()),
            ])
            ->add('phone', TextType::class, [
                'label' => 'Tél. fixe',
                'required' => false,
                'constraints' => [
                    new Length(['max' => 20]),
                ],
                'attr' => [
                    'size' => 20,
                    'maxlength' => 20,
                ],
            ])
            ->add('mobilephone', TextType::class, [
                'label' => 'Tél. portable',
                'required' => false,
                'constraints' => [
                    new Length(['max' => 20]),
                ],
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
            ->add('username', TextType::class, [
                'label' => 'Login',
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 30]),
                ],
                'attr' => [
                    'size' => 30,
                    'maxlength' => 30,
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'required' => false,
                'invalid_message' => 'The password fields must match',
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
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

        $builder->get('roles')->addModelTransformer(new CallbackTransformer(
            fn ($rolesAsArray): string => json_encode($rolesAsArray),
            fn ($rolesAsString): array => json_decode((string) $rolesAsString)
        ));

        $builder
            ->get('companyId')->addModelTransformer(new CallbackTransformer(
                fn ($value): string => (string) $value,
                fn ($value): int => (int) $value
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
