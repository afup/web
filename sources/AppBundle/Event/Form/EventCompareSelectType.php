<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Form\Support\EventHelper;
use AppBundle\Event\Model\Event;
use CCMBenchmark\Ting\Repository\Collection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventCompareSelectType extends AbstractType
{
    private readonly EventHelper $eventHelper;

    public function __construct()
    {
        $this->eventHelper = new EventHelper();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $eventId = $builder->getData()['event_id'];
        $choices = $this->buildChoices($options['events']);
        $builder
            ->add('event_id', ChoiceType::class, [
                'choices' => $choices,
                'group_by' => fn ($choice, string $key): string => $this->eventHelper->groupByYear($key),
            ])
            ->add('compared_event_id', ChoiceType::class, [
                'choices' => $choices,
                'choice_attr' => function ($choice) use ($eventId) {
                    if ($choice === $eventId) {
                        return ['disabled' => true];
                    }

                    return [];
                },
                'group_by' => fn ($choice, string $key): string => $this->eventHelper->groupByYear($key),
            ])
            ->setMethod(Request::METHOD_GET);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
        $resolver->setRequired([
            'events',
        ]);
        $resolver->setAllowedTypes('events', [Collection::class]);
    }

    public function getBlockPrefix(): string
    {
        return '';
    }

    /**
     * @param Collection<Event> $eventCollection
     * @return array<string,int>
     */
    private function buildChoices(Collection $eventCollection): array
    {
        $events = $this->eventHelper->sortEventsByStartDate(
            iterator_to_array($eventCollection),
        );

        $choices = [];

        foreach ($events as $event) {
            $choices[$event->getTitle()] = $event->getId();
        }

        return $choices;
    }
}
