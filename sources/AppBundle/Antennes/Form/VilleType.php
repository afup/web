<?php

declare(strict_types=1);

namespace AppBundle\Antennes\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<VilleFormData>
 */
final class VilleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $points = [
            'premierPoint' => 'Premier point',
            'deuxiemePoint' => 'Deuxième point',
            'troisiemePoint' => 'Troisième point',
        ];

        foreach ($points as $point => $label) {
            $builder
                ->add($point . 'X', IntegerType::class, ['label' => $label . ' (x)', 'required' => false])
                ->add($point . 'Y', IntegerType::class, ['label' => $label . ' (y)', 'required' => false]);
        }

        // html5 : la valeur est rendue et lue au format « 45.759723 », quelle que soit la locale
        $coordonnee = ['scale' => 6, 'html5' => true, 'required' => false, 'attr' => ['step' => 'any']];

        $builder
            ->add('latitude', NumberType::class, ['label' => 'Latitude'] + $coordonnee)
            ->add('longitude', NumberType::class, ['label' => 'Longitude'] + $coordonnee);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => VilleFormData::class,
            'error_bubbling' => false,
        ]);
    }
}
