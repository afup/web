<?php

namespace AppBundle\Association\Form;

use AppBundle\Event\Model\TicketEventType as ModelTicketEventType;
use AppBundle\Event\Model\TicketType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

class TicketEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ticketType', ChoiceType::class, [
                'choices' => $this->ticketTypesToChoices($options['ticketTypes']),
                'constraints' => [
                    new NotBlank()
                ],
                'label' => 'ticket.ticket_type'
            ])
            ->add('price', MoneyType::class, [
                'currency' => '',
                'constraints' => [
                    new GreaterThanOrEqual(0),
                    new NotBlank()
                ],
                'label' => 'ticket.price'
            ])
            ->add('dateStart', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank()
                ],
                'label' => 'ticket.date_start'
            ])
            ->add('dateEnd', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank()
                ],
                'label' => 'ticket.date_end'
            ])
            ->add('description', TextareaType::class, [
                'constraints' => [
                    new NotBlank()
                ],
                'label' => 'ticket.description'
            ])
            ->add('maxTickets', IntegerType::class, [
                'constraints' => [
                    new GreaterThanOrEqual(0),
                ],
                'required' => false,
                'label' => 'ticket.max_tickets'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ModelTicketEventType::class
        ]);
        $resolver->setRequired([
            'ticketTypes'
        ]);
    }

    private function ticketTypesToChoices($ticketTypes)
    {
        $choices = [];

        /** @var TicketType $ticketType */
        foreach ($ticketTypes as $ticketType) {
            $choices[(string) $ticketType] = $ticketType;
        }

        return $choices;
    }
}
