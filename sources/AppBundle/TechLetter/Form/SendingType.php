<?php


namespace AppBundle\TechLetter\Form;

use AppBundle\TechLetter\Model\Sending;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SendingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sendingDate', DateType::class, ['label' => 'Date planifiée', 'data' => new \DateTime('next wednesday')])
            ->add('save', SubmitType::class, ['label' => 'Créer cette techletter'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sending::class,
        ]);
    }
}
