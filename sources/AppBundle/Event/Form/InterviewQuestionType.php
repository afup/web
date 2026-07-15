<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Entity\InterviewQuestion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @extends AbstractType<InterviewQuestion>
 */
class InterviewQuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('question', TextareaType::class, [
                'label' => 'Question',
                'attr' => ['rows' => 2],
                'required' => true,
                'empty_data' => '',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ],
            ])
            ->add('reponse', TextareaType::class, [
                'label' => 'Réponse',
                'attr' => ['rows' => 10],
                'required' => true,
                'empty_data' => '',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InterviewQuestion::class,
        ]);
    }
}
