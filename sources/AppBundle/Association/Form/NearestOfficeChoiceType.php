<?php

declare(strict_types=1);

namespace AppBundle\Association\Form;

use AppBundle\Offices\OfficesCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NearestOfficeChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $officesCollection = new OfficesCollection();
        $offices = ['-Aucune-' => ''];
        foreach ($officesCollection->getOrderedLabelsByKey() as $key => $label) {
            $offices[$label] = $key;
        }

        $resolver->setDefaults(['choices' => $offices]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
