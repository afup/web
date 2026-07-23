<?php

declare(strict_types=1);

namespace AppBundle\Planete;

use AppBundle\Association\Model\Repository\UserRepository;
use PlanetePHP\FeedStatus;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class FeedFormType extends AbstractType
{
    public function __construct(private readonly UserRepository $userRepository) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $users = ['' => null];
        foreach ($this->userRepository->search() as $user) {
            $users[$user->getLastName() . ' ' . $user->getFirstName()] = $user->getId();
        }
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'required' => true,
                'attr' => [
                    'size' => 30,
                    'maxlength' => 40,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('url', UrlType::class, [
                'label' => 'URL',
                'required' => true,
                'attr' => [
                    'size' => 50,
                    'maxlength' => 200,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Url(),
                ],
            ])
            ->add('feed', UrlType::class, [
                'label' => 'Flux',
                'required' => true,
                'attr' => [
                    'size' => 50,
                    'maxlength' => 200,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Url(),
                ],
            ])
            ->add('userId', ChoiceType::class, [
                'label' => 'Personne physique',
                'required' => false,
                'choices' => $users,
            ])
            ->add('status', EnumType::class, [
                'label' => 'État',
                'required' => true,
                'class' => FeedStatus::class,
                'constraints' => [
                    new Assert\NotNull(),
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Ajouter']);
    }
}
