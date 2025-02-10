<?php

declare(strict_types=1);


namespace AppBundle\TechLetter\Form;

use AppBundle\TechLetter\Model\Sending;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SendingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sendingDate', DateType::class, ['label' => 'Date planifiée', 'data' => new \DateTime('next wednesday')])
            ->add('save', SubmitType::class, ['label' => 'Créer cette techletter'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sending::class,
        ]);
    }
}
