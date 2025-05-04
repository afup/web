<?php

declare(strict_types=1);


namespace AppBundle\Association\Form;

use AppBundle\Association\Model\CompanyMember;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrue as RecaptchaIsValid;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [];
        for ($i = 1; $i <=10; $i++) {
            $j = $i*AFUP_PERSONNE_MORALE_SEUIL;
            $choices[$j . ' (' . $i*AFUP_COTISATION_PERSONNE_MORALE . '€)'] = $j;
        }

        $builder
            ->add('companyName', TextType::class, ['label' => 'Company'])
            ->add('firstName', TextType::class, ['label' => 'Firstname'])
            ->add('lastName', TextType::class, ['label' => 'Lastname'])
            ->add('email', EmailType::class)
            ->add('siret', TextType::class)
            ->add('address', TextareaType::class)
            ->add('zipcode', TextType::class, ['label' => 'Zip code'])
            ->add('city', TextType::class)
            ->add('phone', TextType::class)
            ->add(
                'maxMembers',
                ChoiceType::class,
                [
                    'choices' => $choices,
                ]
            )
            ->add('invitations', CollectionType::class, [
                // each entry in the array will be an "email" field
                'entry_type'   => CompanyMemberInvitationType::class,
                'allow_add' => true,
                'required' => false,
            ])
            ->add('recaptcha', EWZRecaptchaType::class, [
                'label' => 'Vérification',
                'mapped' => false,
                'constraints' => [
                    new RecaptchaIsValid(),
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'saveMembership'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CompanyMember::class,
        ]);
    }
}
