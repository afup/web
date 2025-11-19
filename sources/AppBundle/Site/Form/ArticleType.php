<?php

declare(strict_types=1);

namespace AppBundle\Site\Form;

use Afup\Site\Corporate\Article;
use AppBundle\Event\Entity\Event;
use AppBundle\Site\Entity\Rubric;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToTimestampTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class ArticleType extends AbstractType
{
    public const POSITIONS_RUBRIQUES = 9;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $positions = [];
        for ($i = self::POSITIONS_RUBRIQUES ; $i >= -(self::POSITIONS_RUBRIQUES); $i--) {
            $positions[$i] = $i;
        }

        /** @var \AppBundle\Site\Model\Article|\AppBundle\Site\Entity\Article|null $article */
        $article = $builder->getData();
        $textareaCssClass = 'simplemde';
        if ($article !== null && $article->isContentTypeMarkdown() === false) {
            $textareaCssClass = 'tinymce';
        }

        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre de l\'article',
                'required' => true,
                'attr' => [
                    'maxlength' => 255,
                    'size' => 60,
                ],
                'constraints' => [
                    new Assert\Length(max: 255),
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ],
            ])
            ->add('leadParagraph', TextareaType::class, [
                'label' => 'Chapeau',
                'required' => false,
                'attr' => [
                    'cols' => 42,
                    'rows' => 10,
                    'class' => $textareaCssClass,
                ],
                'constraints' => [
                    new Assert\Type('string'),
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'required' => true,
                'attr' => [
                    'cols' => 42,
                    'rows' => 20,
                    'class' => $textareaCssClass,
                ],
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ],
            ])
            ->add('path', TextType::class, [
                'required' => true,
                'label' => 'Raccourci',
                'attr' => [
                    'maxlength' => 255,
                    'size' => 60,
                ],
                'constraints' => [
                    new Assert\Length(max: 255),
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                    new Assert\Regex('/(\s)/', 'Ne doit pas contenir d\'espaces', null, false),
                ],
            ])
            ->add('rubric', EntityType::class, [
                'label' => 'Rubrique',
                'class' => Rubric::class,
                'choice_label' => 'name',
                'required' => true,
            ])
            ->add('publishedAt', DateTimeType::class, [
                'required' => false,
                'html5' => true,
                'label' => 'Date',
                'input' => 'timestamp',
                'widget' => 'single_text',
                'years' => range(2001, date('Y')),
                'attr' => [
                    'style' => 'display: flex;',
                ],
                'constraints' => [
                    new Assert\Type("datetime"),
                ],
            ])
            ->add('position', ChoiceType::class, [
                'required' => false,
                'label' => 'Position',
                'choices' => $positions,
                'translation_domain' => false,
                'placeholder' => false,
                'constraints' => [
                    new Assert\Type("integer"),
                ],
            ])
            ->add('state', ChoiceType::class, [
                'label' => 'Etat',
                'required' => false,
                'choices' => [
                    'Hors ligne' => -1,
                    'En attente' => 0,
                    'En ligne' => 1,
                ],
                'placeholder' => false,
                'constraints' => [
                    new Assert\Type("integer"),
                ],
            ])
            ->add('theme', ChoiceType::class, [
                'label' => 'Thème',
                'required' => false,
                'choices' => array_flip(Article::getThemesLabels()),
                'constraints' => [
                    new Assert\Type("integer"),
                ],
            ])
            ->add('event', EntityType::class, [
                'label' => 'Événement',
                'required' => false,
                'class' => Event::class,
                'choice_label' => 'title',
            ])
        ;
        $builder->get('publishedAt')->addModelTransformer(new DateTimeToTimestampTransformer());
    }
}
