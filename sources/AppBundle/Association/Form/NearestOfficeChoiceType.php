<?php

declare(strict_types=1);

namespace AppBundle\Association\Form;

use AppBundle\Antennes\AntennesCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NearestOfficeChoiceType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $antennesCollection = new AntennesCollection();
        $offices = ['-Aucune-' => ''];
        foreach ($antennesCollection->getAllSortedByLabels() as $antenne) {
            $offices[$antenne->label] = $antenne->code;
        }

        $resolver->setDefaults(['choices' => $offices]);
    }

    public function getParent()
    {
        return ChoiceType::class;
    }
}
