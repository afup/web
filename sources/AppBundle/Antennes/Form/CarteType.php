<?php

declare(strict_types=1);

namespace AppBundle\Antennes\Form;

use AppBundle\Antennes\AncrageLegende;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<CarteFormData>
 */
final class CarteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('afficherSurCarte', CheckboxType::class, [
                'label' => 'Afficher sur la carte',
                'required' => false,
            ])
            ->add('utiliserSecondeCouleur', CheckboxType::class, [
                'label' => 'Utiliser la seconde couleur',
                'help' => 'Pour distinguer deux antennes voisines sur la carte.',
                'required' => false,
            ])
            ->add('ancrageLegende', EnumType::class, [
                'label' => 'Ancrage de la légende',
                'class' => AncrageLegende::class,
                'choice_label' => fn(AncrageLegende $ancrage): string => match ($ancrage) {
                    AncrageLegende::Haut => 'En haut',
                    AncrageLegende::Bas => 'En bas',
                    AncrageLegende::Gauche => 'À gauche',
                    AncrageLegende::Droite => 'À droite',
                },
                'required' => false,
                'placeholder' => false,
            ])
            ->add('premiereVille', VilleType::class, [
                'label' => 'Première ville',
            ])
            ->add('secondeVille', VilleType::class, [
                'label' => 'Seconde ville',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CarteFormData::class,
            'error_bubbling' => false,
        ]);
    }
}
