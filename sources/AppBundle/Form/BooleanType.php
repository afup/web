<?php

declare(strict_types=1);

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BooleanType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'expanded' => true,
            'row_attr' => ['class' => 'fields inline'],
            'choices' => [
                'Oui' => true,
                'Non' => false,
            ],
        ]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
