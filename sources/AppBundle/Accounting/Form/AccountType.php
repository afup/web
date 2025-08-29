<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('name', TextType::class, [
            'label' => 'Nom du compte',
            'required' => true,
            'constraints' => [
                new Assert\NotBlank(),
                new Assert\Type('string'),
            ],
        ]);
    }
}
