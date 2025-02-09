<?php

declare(strict_types=1);


namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventSelectType extends AbstractType
{
    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id', ChoiceType::class,
                [
                    'choice_label' => 'title',
                    'choice_value' => 'id',
                    'data' => $options['data'] ?? null,
                    'choices' => $this->eventRepository->getAll()
                ]
            )
            ->setMethod(Request::METHOD_GET)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'csrf_protection' => false
        ]);
    }
}
