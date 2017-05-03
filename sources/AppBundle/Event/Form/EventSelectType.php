<?php


namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventSelectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['event_repository'] === null) {
            throw new \RuntimeException('This type needs a "event_repository" option');
        }

        $builder
            ->add('id', ChoiceType::class,
                [
                    'choice_label' => 'title',
                    'choice_value' => 'id',
                    'data' => $options['data'],
                    'choices' => $options['event_repository']->getAll()
                ]
            )
            ->setMethod(Request::METHOD_GET)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'event_repository' => null,
            'csrf_protection' => false
        ]);
    }
}
