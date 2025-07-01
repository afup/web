<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Form\Support\EventHelper;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\EventTheme;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class EventThemeType extends AbstractType
{
    private readonly EventHelper $eventHelper;

    public function __construct(private readonly EventRepository $eventRepository)
    {
        $this->eventHelper = new EventHelper();
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $idForumField = $builder->create('idForum', ChoiceType::class, [
            'label' => 'Évènement',
            'choice_label' => 'title',
            'choice_value' => fn(?Event $event): ?string => $event?->getId() !== null ? (string) $event->getId() : null,
            'choices' => $this->eventHelper->sortEventsByStartDate(
                iterator_to_array($this->eventRepository->getAllActive()),
            ),
            'group_by' => fn(Event $choice): string => $this->eventHelper->groupByYear($choice),
        ]);

        $idForumField->addModelTransformer(new CallbackTransformer(
            fn(?int $idForum): ?Event => $idForum ? $this->eventRepository->getOneBy(['id' => $idForum]) : null,
            fn(?Event $event): ?int => $event?->getId(),
        ));
        $builder->add($idForumField)
            ->add('name', TextType::class, [
                'label' => 'Nom du thème',
                'constraints' => [new Assert\NotBlank(['message' => 'Titre du forum manquant'])],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'help' => 'Le thème de description apparait sur la page de programme',
                'constraints' => [new Assert\NotBlank(), new Assert\Length(['max' => 600])],
                'attr' => ['class' => 'simplemde'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EventTheme::class,
        ]);
    }
}
