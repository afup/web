<?php

declare(strict_types=1);

namespace AppBundle\Association\Form;

use AppBundle\Association\Model\CompanyMemberInvitation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyMemberInvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // empty_data explicite : TextType le fait valoir null quand le champ n'est pas
            // requis, ce qui ferait échouer setEmail(string) sur un email laissé vide.
            ->add('email', EmailType::class, ['empty_data' => ''])
            ->add('manager', CheckboxType::class, [
                'required' => false,
                'label' => 'Lui partager les droits de gestion',
                // disabled fait aussi ignorer la valeur soumise au profit de celle du modèle :
                // le verrou résiste à un POST forgé.
                'disabled' => $options['lock_manager'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CompanyMemberInvitation::class,
            'lock_manager' => false,
        ]);

        $resolver->setAllowedTypes('lock_manager', 'bool');
    }
}
