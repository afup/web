<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Event;
use CCMBenchmark\Ting\Repository\Collection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventCompareSelectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $eventId = $builder->getData()['event_id'];
        $choices = $this->buildChoices($options['events']);
        $builder
            ->add('event_id', ChoiceType::class, [
                'choices' => $choices,
                'group_by' => static fn ($choice, $key) => self::groupBy($key),
            ])
            ->add('compared_event_id', ChoiceType::class, [
                'choices' => $choices,
                'choice_attr' => function ($choice, $key, $value) use ($eventId) {
                    if ($choice === $eventId) {
                        return ['disabled' => true];
                    }

                    return [];
                },
                'group_by' => static fn ($choice, $key) => self::groupBy($key),
            ])
            ->setMethod(Request::METHOD_GET);
    }

    private static function groupBy($key): string
    {
        if (preg_match('/\d{4}/', $key, $matches)) {
            return $matches[0];
        }

        return '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => false
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
     * @param Collection<Event> $events
     * @return array<string,int>
     */
    private function buildChoices(Collection $events): array
    {
        /** @var array<Event> $data */
        $data = iterator_to_array($events);
        usort($data, static fn (Event $a, Event $b) => $a->getDateStart() <= $b->getDateStart());

        $choices = [];
        foreach ($data as $event) {
            $choices[$event->getTitle()] = $event->getId();
        }
        return $choices;
    }
}
