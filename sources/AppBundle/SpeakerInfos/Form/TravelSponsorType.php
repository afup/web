<?php

declare(strict_types=1);

namespace AppBundle\SpeakerInfos\Form;

use AppBundle\Event\Model\Speaker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class TravelSponsorType extends AbstractType
{
    public const OPTION_SPONSORED = 'sponsored';
    public const OPTION_NOT_NEEDED = 'not_needed';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'choices',
                ChoiceType::class,
                [
                    'expanded' => true,
                    'multiple' => true,
                    'choices' => [
                        'speaker_infos.travel_expenses.checkbox_label.not_needed' => self::OPTION_NOT_NEEDED,
                        'speaker_infos.travel_expenses.checkbox_label.sponsored' => self::OPTION_SPONSORED,
                    ],
                ],
            )
            ->add('submit', SubmitType::class, ['label' => 'Enregistrer']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }

    /**
     * @return array{choices: array<self::OPTION_*>}|null
     */
    public static function buildDefaultFromSpeaker(Speaker $speaker): ?array
    {
        $values = [];

        if (!$speaker->isTravelRefundNeeded()) {
            $values[] = self::OPTION_NOT_NEEDED;
        }

        if ($speaker->isTravelRefundSponsored()) {
            $values[] = self::OPTION_SPONSORED;
        }

        if (count($values) === 0) {
            return null;
        }

        return [
            'choices' => $values,
        ];
    }
}
