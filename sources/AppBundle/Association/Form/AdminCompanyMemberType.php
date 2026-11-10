<?php

declare(strict_types=1);

namespace AppBundle\Association\Form;

use AppBundle\Association\Entity\CompanyMember;
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
            ->add('companyName', TextType::class, ['label' => 'Company', 'empty_data' => ''])
            ->add('firstName', TextType::class, ['label' => 'Firstname', 'empty_data' => ''])
            ->add('lastName', TextType::class, ['label' => 'Lastname', 'empty_data' => ''])
            ->add('email', EmailType::class, ['empty_data' => ''])
            ->add('siret', TextType::class, ['empty_data' => ''])
            ->add('address', TextareaType::class, ['empty_data' => ''])
            ->add('zipcode', TextType::class, ['label' => 'Zip code', 'property_path' => 'zipCode', 'empty_data' => ''])
            ->add('city', TextType::class, ['empty_data' => ''])
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
