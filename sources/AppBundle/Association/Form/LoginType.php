<?php

declare(strict_types=1);

namespace AppBundle\Association\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Le POST est intercepté par le firewall (cf. legacy_secured_area dans config/packages/security.yaml) : ce
 * formulaire n'est jamais soumis à Symfony Forms, il n'est là que pour le rendu. Les noms des champs sont donc
 * un contrat avec la configuration du firewall, qui reste inchangée : d'où le préfixe vide (les champs sont
 * postés à plat, sans préfixe de formulaire) et le jeton CSRF nommé et identifié comme l'attend le firewall.
 *
 * @extends AbstractType<array<string, mixed>>
 */
class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('utilisateur', TextType::class, [
                'label' => "Email ou nom d'utilisateur",
            ])
            ->add('mot_de_passe', PasswordType::class, [
                'label' => 'Mot de passe',
            ])
            ->add('_target_path', HiddenType::class)
            ->add('submit', SubmitType::class, ['label' => 'Se connecter'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_field_name' => '_csrf_token',
            'csrf_token_id' => 'authenticate',
        ]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
