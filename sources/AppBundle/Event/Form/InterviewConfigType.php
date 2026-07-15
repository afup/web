<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Model\Event;
use AppBundle\Event\Wordpress\WordpressClient;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @extends AbstractType<Event>
 */
final class InterviewConfigType extends AbstractType
{
    public function __construct(
        private readonly WordpressClient $wordpressClient,
        #[Autowire('%env(WORDPRESS_BASE_URI)%')]
        private readonly string $wordpressBaseUri,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $choices = [];
        foreach ($this->wordpressClient->listCategories() as $category) {
            $choices[$category->name] = $category->id;
        }

        $builder
            ->add('interviewsWordpressCategoryId', ChoiceType::class, [
                'label' => 'Catégorie',
                'required' => true,
                'choices' => $choices,
                'placeholder' => '-- Choisir --',
                'help' => 'La catégorie doit être créée <a href="' . $this->wordpressBaseUri . '/wp-admin/edit-tags.php?taxonomy=category">côté WordPress</a>',
                'help_html' => true,
            ])
            ->add('interviewsIntro', TextareaType::class, [
                'label' => "Texte d'introduction des articles",
                'attr' => ['rows' => 5],
                'required' => true,
                'empty_data' => '',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ],
            ])
            ->add('interviewsCtaText', TextType::class, [
                'label' => 'Texte du bouton de la billetterie',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
            'csrf_protection' => false,
        ]);
    }
}
