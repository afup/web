<?php

declare(strict_types=1);

namespace AppBundle\Planete;

use AppBundle\Association\Model\Repository\UserRepository;
use PlanetePHP\Feed;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class FeedFormType extends AbstractType
{
    public function __construct(private readonly UserRepository $userRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $users = [null => ''];
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
            ])
            ->add('url', UrlType::class, [
                'label' => 'URL',
                'required' => true,
                'attr' => [
                    'size' => 50,
                    'maxlength' => 200,
                ],
            ])
            ->add('feed', UrlType::class, [
                'label' => 'Flux',
                'required' => true,
                'attr' => [
                    'size' => 50,
                    'maxlength' => 200,
                ],
            ])
            ->add('userId', ChoiceType::class, [
                'label' => 'Personne physique',
                'required' => false,
                'choices' => $users,
            ])
            ->add('status', ChoiceType::class, [
                'label' => 'Etat',
                'required' => true,
                'choices' => [
                    'Actif' => Feed::STATUS_ACTIVE,
                    'Inactif' => Feed::STATUS_INACTIVE,
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Ajouter']);
    }
}
