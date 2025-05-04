<?php

declare(strict_types=1);

namespace AppBundle\Association\Form;

use AppBundle\Event\Model\Repository\BadgeRepository;
use AppBundle\Event\Model\Repository\UserBadgeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserBadgeType extends AbstractType
{
    public function __construct(
        private readonly BadgeRepository $badgeRepository,
        private readonly UserBadgeRepository $userBadgeRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $userBadgesIds = [];
        foreach ($this->userBadgeRepository->findByUserId($options['user']->getId()) as $userBadge) {
            $userBadgesIds[$userBadge->getBadge()->getId()] = $userBadge->getBadge()->getId();
        }


        $badges = [];
        foreach ($this->badgeRepository->getAll() as $badge) {
            if (isset($userBadgesIds[$badge->getId()])) {
                continue;
            }
            $badges[$badge->getLabel()] = $badge->getId();
        }

        $builder
            ->add(
                'badge',
                ChoiceType::class,
                [
                    'label' => 'Badge',
                    'choices' => $badges,
                ]
            )
            ->add(
                'date',
                DateType::class,
                [
                    'label' => 'Date',
                ]
            )
            ->add(
                'user',
                HiddenType::class,
                [
                    'data' => $options['user']->getId(),
                ]
            )
            ->add(
                'save',
                SubmitType::class,
                [
                    'label' => 'Créer',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['user']);
    }
}
