<?php

declare(strict_types=1);

namespace AppBundle\Association\Form;

use AppBundle\Antennes\Entity\Repository\AntenneRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NearestOfficeChoiceType extends AbstractType
{
    public function __construct(private readonly AntenneRepository $antennesRepository) {}

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $offices = ['-Aucune-' => ''];
        foreach ($this->antennesRepository->getAllSortedByLabels() as $antenne) {
            $offices[$antenne->libelle] = $antenne->code;
        }

        $resolver->setDefaults(['choices' => $offices]);
    }

    #[\Override]
    public function getParent(): string
    {
        return ChoiceType::class;
    }
}
