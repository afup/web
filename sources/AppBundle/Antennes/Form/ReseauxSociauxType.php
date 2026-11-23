<?php

declare(strict_types=1);

namespace AppBundle\Antennes\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<ReseauxSociauxFormData>
 */
final class ReseauxSociauxType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('blog', UrlType::class, [
                'label' => 'Blog',
                'default_protocol' => 'https',
                'required' => false,
            ])
            ->add('youtube', UrlType::class, [
                'label' => 'YouTube',
                'default_protocol' => 'https',
                'help' => 'URL de la chaîne (ex. https://www.youtube.com/@afuplyon)',
                'required' => false,
            ])
            ->add('linkedin', TextType::class, [
                'label' => 'LinkedIn',
                'help' => 'Identifiant de la page entreprise (ex. afup-lyon pour https://www.linkedin.com/company/afup-lyon/)',
                'required' => false,
            ])
            ->add('bluesky', TextType::class, [
                'label' => 'Bluesky',
                'help' => 'Identifiant du compte (ex. lyon.afup.org)',
                'required' => false,
            ])
            ->add('twitter', TextType::class, [
                'label' => 'Twitter',
                'help' => 'Identifiant du compte, sans @ (ex. AFUP_Lyon)',
                'required' => false,
            ])
            ->add('github', TextType::class, [
                'label' => 'GitHub',
                'required' => false,
            ])
            ->add('discord', UrlType::class, [
                'label' => 'Discord',
                'default_protocol' => 'https',
                'required' => false,
            ])
            ->add('cfp', UrlType::class, [
                'label' => 'CFP',
                'default_protocol' => 'https',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ReseauxSociauxFormData::class,
        ]);
    }
}
