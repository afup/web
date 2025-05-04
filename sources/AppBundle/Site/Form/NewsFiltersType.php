<?php

declare(strict_types=1);

namespace AppBundle\Site\Form;

use Afup\Site\Corporate\Article;
use AppBundle\Site\Model\Repository\ArticleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsFiltersType extends AbstractType
{
    public function __construct(private readonly ArticleRepository $articleRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $yearValues = [];
        foreach ($this->articleRepository->getAllYears() as $year) {
            $yearValues[$year] = $year;
        }

        $builder
            ->setMethod(Request::METHOD_GET)
            ->add(
                'year',
                ChoiceType::class,
                    [
                        'label' => 'Année',
                        'multiple' => true,
                        'expanded' => true,
                        'choices' => $yearValues,
                    ]
            )
            ->add(
                'theme',
                ChoiceType::class,
                [
                    'label' => 'Thème',
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => array_flip(Article::getThemesLabels()),
                ]
            )
            ->add(
                'event',
                ChoiceType::class,
                [
                    'label' => 'Cycle de conférences',
                    'multiple' => true,
                    'expanded' => true,
                    'choices' => array_flip($this->articleRepository->getEventsLabelsById()),
                ]
            )
            ->add('submit', SubmitType::class, ['label' => 'Filtrer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection'   => false,
        ]);
    }
}
