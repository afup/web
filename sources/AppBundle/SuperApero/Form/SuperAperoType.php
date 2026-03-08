<?php

declare(strict_types=1);

namespace AppBundle\SuperApero\Form;

use AppBundle\Antennes\AntenneRepository;
use AppBundle\SuperApero\Entity\Repository\SuperAperoRepository;
use AppBundle\SuperApero\Entity\SuperApero;
use AppBundle\SuperApero\Entity\SuperAperoMeetup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SuperAperoType extends AbstractType
{
    public function __construct(
        private readonly SuperAperoRepository $superAperoRepository,
        private readonly AntenneRepository $antenneRepository,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $antennes = $this->antenneRepository->getAllSortedByLabels();

        $builder
            ->add('date', DateType::class, [
                'label' => 'Date',
                'widget' => 'single_text',
                'input' => 'datetime_immutable',
                'required' => true,
            ])
            ->add($builder->create('meetups', FormType::class, [
                'mapped' => false,
                'label' => false,
            ]));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($antennes): void {
            /** @var SuperApero $superApero */
            $superApero = $event->getData();

            $meetupsForm = $event->getForm()->get('meetups');
            foreach ($antennes as $antenne) {
                $existing = $superApero->meetups[$antenne->code] ?? null;

                $meetupsForm->add($antenne->code, SuperAperoMeetupType::class, [
                    'label' => $antenne->label,
                    'data' => $existing instanceof SuperAperoMeetup
                        ? SuperAperoMeetupFormData::fromEntity($existing)
                        : null,
                ]);
            }
        });

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($antennes): void {
            /** @var SuperApero $superApero */
            $superApero = $event->getData();
            $form = $event->getForm();

            if (isset($superApero->date)) {
                $year = $superApero->annee();
                $existing = $this->superAperoRepository->findOneByYear($year);

                if ($existing !== null && $existing->id !== $superApero->id) {
                    $form->get('date')->addError(
                        new FormError("Un Super Apéro existe déjà pour l'année {$year}."),
                    );
                }
            }

            $meetupsForm = $form->get('meetups');

            $submittedAntennes = [];
            foreach ($antennes as $antenne) {
                /** @var SuperAperoMeetupFormData $data */
                $data = $meetupsForm->get($antenne->code)->getData();

                if ($data->hasValues()) {
                    $submittedAntennes[] = $antenne->code;

                    if (isset($superApero->meetups[$antenne->code])) {
                        $superApero->meetups[$antenne->code]->meetupId = $data->meetupId;
                        $superApero->meetups[$antenne->code]->description = $data->description;
                    } else {
                        $meetup = new SuperAperoMeetup();
                        $meetup->antenne = $antenne->code;
                        $meetup->meetupId = $data->meetupId;
                        $meetup->description = $data->description;
                        $superApero->addMeetup($meetup);
                    }
                }
            }

            foreach ($superApero->meetups->toArray() as $meetup) {
                if (!\in_array($meetup->antenne, $submittedAntennes, true)) {
                    $superApero->removeMeetup($meetup);
                }
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SuperApero::class,
        ]);
    }
}
