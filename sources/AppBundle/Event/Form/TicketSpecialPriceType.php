<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\TicketSpecialPrice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TicketSpecialPriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('token', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 255])
                ]
            ])
            ->add('dateStart', DateType::class, [
                'label' => 'Date de début',
            ])
            ->add('dateEnd', DateType::class, [
                'label' => 'Date de fin',
            ])
            ->add('price', IntegerType::class, [
                'label' => 'Prix (TTC)',
                'constraints' => [
                    new GreaterThan(['value' => 0])
                ]
            ])
            ->add('description', TextType::class, [
                'label' => 'Description (interne)',
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 255])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TicketSpecialPrice::class
        ]);
    }
}
