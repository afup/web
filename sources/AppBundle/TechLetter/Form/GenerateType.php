<?php

namespace AppBundle\TechLetter\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class GenerateType extends AbstractType
{
    const MAX_NB_NEWS = 2;
    const MAX_NB_ARTICLES = 10;
    const MAX_NB_PROJECTS = 5;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add(
                'news',
                CollectionType::class,
                [
                    'entry_type' => TextType::class,
                    'data' => array_fill(0, self::MAX_NB_NEWS, ''),
                    'label' => 'URLs des news sélectionnées',
                    'entry_options' => [
                        'required' => false,
                        'constraints' => [
                            new Url(),
                        ],
                    ]
                ]
            )
            ->add(
                'articles',
                CollectionType::class,
                [
                    'entry_type' => TextType::class,
                    'data' => array_fill(0, self::MAX_NB_ARTICLES, ''),
                    'label' => 'URLs des articles sélectionnées',
                    'entry_options' => [
                        'required' => false,
                        'constraints' => [
                            new Url(),
                        ],
                    ],
                    'constraints' => [
                        new Count(['min' => 1]),
                        new Callback([
                            'callback' => function ($data, ExecutionContextInterface $context) {
                                $data = array_filter($data);
                                if (0 === count($data)) {
                                    $context->buildViolation('Aucun article ajouté')->atPath('articles')->addViolation();
                                }
                            }
                        ])
                    ],
                ]
            )
            ->add(
                'projects',
                CollectionType::class,
                [
                    'entry_type' => TextType::class,
                    'data' => array_fill(0, self::MAX_NB_PROJECTS, ''),
                    'label' => 'URLs des projets sélectionnées',
                    'entry_options' => [
                        'required' => false,
                        'constraints' => [
                            new Url(),
                        ],
                    ]
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Générer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
    }
}
