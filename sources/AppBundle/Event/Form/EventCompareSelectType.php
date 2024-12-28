<?php


namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Repository\EventRepository;
use CCMBenchmark\Ting\Exception;
use CCMBenchmark\Ting\Query\QueryException;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventCompareSelectType extends AbstractType
{
    /** @var EventRepository */
    private $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @throws QueryException
     * @throws Exception
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $eventId = $builder->getData()['id'] ?? null;
        $excludedEventId = $builder->getData()['compared_event_id'] ?? null;
        $events = $this->eventRepository->getAllEventsExcept($eventId);

        dump($events->first());

        $builder
            ->add('compared_event_id', ChoiceType::class,
                [
                    'choice_label' => 'title',
                    'choice_value' => 'id',
                    'data' => "2",
                    'choices' => $events,
                ]
            )
            ->setMethod(Request::METHOD_GET)
            ->add('id', HiddenType::class, [
                'data' => $eventId,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix(): string
    {
        return 'compare_event';
    }
}
