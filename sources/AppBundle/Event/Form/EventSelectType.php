<?php

declare(strict_types=1);


namespace AppBundle\Event\Form;

use AppBundle\Event\Form\Support\EventHelper;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventSelectType extends AbstractType
{
    private readonly EventHelper $eventHelper;

    public function __construct(private readonly EventRepository $eventRepository)
    {
        $this->eventHelper = new EventHelper();
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', ChoiceType::class,
                [
                    'choice_label' => 'title',
                    'choice_value' => 'id',
                    'data' => $options['data'] ?? null,
                    'choices' => $this->eventHelper->sortEventsByStartDate(
                        iterator_to_array($this->eventRepository->getAllActive()),
                    ),
                    'group_by' => fn (Event $choice): string => $this->eventHelper->groupByYear($choice),
                ]
            )
            ->setMethod(Request::METHOD_GET)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'csrf_protection' => false,
        ]);
    }
}
