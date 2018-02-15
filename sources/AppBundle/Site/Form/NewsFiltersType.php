<?php

namespace AppBundle\Site\Form;

use Afup\Site\Corporate\Article;
use AppBundle\Site\Model\Repository\ArticleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsFiltersType extends AbstractType
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @param ArticleRepository $articleRepository
     */
    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $yearValues = [];
        foreach ($this->articleRepository->getAllYears() as $year) {
            $yearValues[$year] = $year;
        }

        $builder
            ->setMethod('GET')
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
            ->add('submit', SubmitType::class, ['label' => 'Filtrer'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection'   => false,
        ]);
    }
}
