<?php

declare(strict_types=1);

namespace AppBundle\Association\Form;

use Afup\Site\Utils\Pays;
use AppBundle\Association\Genre;
use AppBundle\Association\Model\Repository\CompanyMemberRepository;
use AppBundle\Association\Model\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserEditType extends AbstractType
{
    private const array MODULE_LEVEL_CHOICES = [
        'Aucun' => User::LEVEL_NONE,
        'Lecteur' => User::LEVEL_READER,
        'Gestionnaire' => User::LEVEL_MANAGER,
    ];

    /** Rôles hors matrice des modules, attribuables par case à cocher. */
    private const array SPECIAL_ROLES = [
        'Super administrateur' => 'ROLE_SUPER_ADMIN',
        'Gestionnaire de la personne morale' => 'ROLE_COMPANY_MANAGER',
    ];

    public function __construct(
        private readonly CompanyMemberRepository $companyMemberRepository,
        private readonly Pays $pays,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('companyId', ChoiceType::class, [
                'label' => 'Personne morale',
                'required' => false,
                'choices' => array_flip($this->companyMemberRepository->getList()),
            ])
            ->add('genre', EnumType::class, [
                'required' => false,
                'class' => Genre::class,
                'label' => 'Genre',
                'placeholder' => 'Ne se prononce pas',
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
                    new Length(max: 20),
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
                    new Length(max: 20),
                ],
                'attr' => [
                    'size' => 20,
                    'maxlength' => 20,
                ],
            ])
            ->add('adminAccess', CheckboxType::class, [
                'label' => "Accès à l'admin",
                'required' => false,
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
                    new Length(max: 30),
                ],
                'attr' => [
                    'size' => 30,
                    'maxlength' => 30,
                ],
            ]);

        if ($options['can_edit_password']) {
            $builder->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'required' => false,
                'invalid_message' => 'The password fields must match',
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ]);
        }

        $builder
            ->add('specialRoles', ChoiceType::class, [
                'label' => 'Rôles',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => self::SPECIAL_ROLES,
                'choice_attr' => fn(string $role): array => $role === 'ROLE_SUPER_ADMIN' && !$options['can_grant_super_admin'] ? ['disabled' => 'disabled'] : [],
            ])
            ->add('needsUpToDateMembership', CheckboxType::class, [
                'label' => 'Nécessite une cotisation à jour',
                'required' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Ajouter']);

        $access = $builder->create('access', FormType::class, [
            'label' => 'Accès aux modules',
            'inherit_data' => true,
        ]);
        foreach (User::MODULES as $module => $label) {
            $access->add($module, ChoiceType::class, [
                'label' => $label,
                'expanded' => true,
                'choices' => self::MODULE_LEVEL_CHOICES,
                'getter' => fn(User $user): int => $user->getModuleLevel($module),
                'setter' => fn(User $user, ?int $level) => $user->setModuleLevel($module, (int) $level),
            ]);
        }
        $builder->add($access);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event): void {
            /** @var User $user */
            $user = $event->getData();
            $event->getForm()->get('specialRoles')->setData(array_values(array_intersect(self::SPECIAL_ROLES, $user->getRoles())));
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($options): void {
            /** @var User $user */
            $user = $event->getData();
            /** @var list<string> $selectedRoles */
            $selectedRoles = $event->getForm()->get('specialRoles')->getData() ?? [];
            foreach (self::SPECIAL_ROLES as $role) {
                if ($role === 'ROLE_SUPER_ADMIN' && !$options['can_grant_super_admin']) {
                    continue;
                }
                $user->setRoleGranted($role, in_array($role, $selectedRoles, true));
            }
        });

        $builder
            ->get('companyId')->addModelTransformer(new CallbackTransformer(
                fn($value): string => (string) $value,
                fn($value): int => (int) $value,
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'can_grant_super_admin' => false,
            'can_edit_password' => true,
        ]);
        $resolver->setAllowedTypes('can_grant_super_admin', 'bool');
        $resolver->setAllowedTypes('can_edit_password', 'bool');
    }
}
