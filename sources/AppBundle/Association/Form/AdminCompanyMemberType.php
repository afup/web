<?php

declare(strict_types=1);


namespace AppBundle\Association\Form;

use AppBundle\Association\Model\CompanyMember;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminCompanyMemberType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
