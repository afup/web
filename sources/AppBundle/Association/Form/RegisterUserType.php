<?php

declare(strict_types=1);

namespace AppBundle\Association\Form;

use AppBundle\Association\Genre;
use AppBundle\Association\Model\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('userCommonInfo', UserType::class, [
                'inherit_data' => true,
                'data_class' => User::class,
            ])
            ->add('country', CountryType::class, [
                'label' => 'Pays',
                'required' => true,
            ])
            ->add('mobilephone', TelType::class, [
                'label' => 'Portable',
                'required' => false,
            ])
            ->add('nearestOffice', NearestOfficeChoiceType::class, [
                'required' => false,
            ])
            ->add('genre', EnumType::class, [
                'required' => false,
                'class' => Genre::class,
                'label' => 'Genre',
                'placeholder' => 'Ne se prononce pas',
            ])
        ;

        $builder->get('userCommonInfo')->add('phone', TextType::class, [
            'required' => false,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
