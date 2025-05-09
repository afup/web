<?php

declare(strict_types=1);

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
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('ticketType', ChoiceType::class, [
                'choices' => $this->ticketTypesToChoices($options['ticketTypes']),
                'constraints' => [
                    new NotBlank(),
                ],
                'label' => 'Type de ticket',
            ])
            ->add('price', MoneyType::class, [
                'currency' => 'EUR',
                'constraints' => [
                    new GreaterThanOrEqual(0),
                    new NotBlank(),
                ],
                'label' => $options['has_prices_defined_with_vat'] ? 'Montant TTC' : 'Montant HT',
            ])
            ->add('dateStart', DateTimeType::class, [
                'widget' => 'choice',
                'constraints' => [
                    new NotBlank(),
                ],
                'label' => 'Date de début',
            ])
            ->add('dateEnd', DateTimeType::class, [
                'widget' => 'choice',
                'constraints' => [
                    new NotBlank(),
                ],
                'label' => 'Date de fin',
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'Description',
            ])
            ->add('maxTickets', IntegerType::class, [
                'constraints' => [
                    new GreaterThanOrEqual(0),
                ],
                'required' => false,
                'label' => 'Nombre max. de tickets',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ModelTicketEventType::class,
            'has_prices_defined_with_vat' => true,
        ]);
        $resolver->setRequired([
            'ticketTypes',
        ]);
    }

    /**
     * @return mixed[]
     */
    private function ticketTypesToChoices($ticketTypes): array
    {
        $choices = [];

        /** @var TicketType $ticketType */
        foreach ($ticketTypes as $ticketType) {
            $choices[$ticketType->getLabel()] = $ticketType;
        }

        return $choices;
    }
}
