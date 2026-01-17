<?php

declare(strict_types=1);

namespace Afup\Tests\Stubs\PHPStan\Doctrine;

use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

final class FormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('stdclass', EntityType::class, [
            'query_builder' => fn(EntityRepository $er) => $er->createQueryBuilder('foo')->where('foo.id = 1'),
        ]);
    }
}
