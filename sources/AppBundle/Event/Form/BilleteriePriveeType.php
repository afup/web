<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Entity\BilleteriePrivee;
use AppBundle\Event\Model\TicketType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * @extends AbstractType<BilleteriePrivee>
 */
class BilleteriePriveeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $ticketTypes = $options['ticketTypes'];
        if (!is_array($ticketTypes)) {
            throw new \RuntimeException('L\'option ticketTypes doit être un tableau.');
        }

        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la billeterie (ex: Billetterie école XYZ)',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('ticketTypeId', ChoiceType::class, [
                'label' => 'Type de place',
                'choices' => $this->ticketTypesToChoices($ticketTypes),
                'choice_value' => 'id',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix de la place',
                'currency' => 'EUR',
                'constraints' => [
                    new GreaterThanOrEqual(0),
                    new NotBlank(),
                ],
            ])
            ->add('maxPlaces', IntegerType::class, [
                'label' => 'Nombre de places disponibles',
                'constraints' => [
                    new GreaterThanOrEqual(1),
                    new NotBlank(),
                ],
            ])
            ->add('dateDebut', DateTimeType::class, [
                'label' => 'Date de début de vente',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('dateFin', DateTimeType::class, [
                'label' => 'Date de fin de vente',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('plainMotDePasse', PasswordType::class, [
                'mapped' => false,
                'required' => $options['is_edit'] === false,
                'label' => $options['is_edit'] ? 'Nouveau mot de passe' : 'Mot de passe',
                'attr' => [
                    'placeholder' => $options['is_edit'] ? 'Laisser vide pour conserver le mot de passe actuel' : '',
                ],
                'constraints' => $options['is_edit'] ? [] : [
                    new NotBlank(),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BilleteriePrivee::class,
            'is_edit' => false,
        ]);
        $resolver->setRequired([
            'ticketTypes',
        ]);
        $resolver->setAllowedTypes('ticketTypes', 'array');
    }

    /**
     * @param array<array-key, mixed> $ticketTypes
     * @return array<string, TicketType>
     */
    private function ticketTypesToChoices(array $ticketTypes): array
    {
        $choices = [];

        foreach ($ticketTypes as $ticketType) {
            if ($ticketType instanceof TicketType) {
                $choices[$ticketType->getLabel()] = $ticketType;
            }
        }

        return $choices;
    }
}
