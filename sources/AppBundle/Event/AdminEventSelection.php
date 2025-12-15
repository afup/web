<?php

declare(strict_types=1);

namespace AppBundle\Event;

use AppBundle\Event\Form\EventSelectType;
use AppBundle\Event\Model\Event;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormView;

final readonly class AdminEventSelection
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        public Event $event,
    ) {}

    public function selectForm(): FormView
    {
        return $this->formFactory->create(EventSelectType::class, $this->event, [
            'data' => $this->event,
        ])->createView();
    }
}
