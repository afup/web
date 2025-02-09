<?php

declare(strict_types=1);


namespace AppBundle\Event\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

class VoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', TextareaType::class, ['required' => false, 'attr' => ['placeholder' => "Facultatif mais très utile\u{a0}!"]])
            ->add('vote', HiddenType::class)
            ->add('sessionId', HiddenType::class)
            ->add('save', SubmitType::class, ['label' => 'Voter'])
        ;
    }
}
