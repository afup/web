<?php

declare(strict_types=1);


namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Room;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoomType extends AbstractType
{
    private string $name = 'room';

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->name = $options['block_prefix'];
        $builder
            ->add('name', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Room::class,
            'block_prefix' => ''
        ]);
    }

    public function getBlockPrefix(): string
    {
        return $this->name ? : parent::getBlockPrefix();
    }
}
