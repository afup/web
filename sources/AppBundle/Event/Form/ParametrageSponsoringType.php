<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Entity\ParametrageSponsoring;
use AppBundle\Event\Enum\ModePageSponsoring;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @extends AbstractType<ParametrageSponsoring>
 */
class ParametrageSponsoringType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('mode', EnumType::class, [
                'label' => 'Mode',
                'class' => ModePageSponsoring::class,
                'expanded' => true,
                'choice_label' => fn(ModePageSponsoring $mode): string => match ($mode) {
                    ModePageSponsoring::Evenement => 'Évènement : redirection vers la page sponsoring du dernier évènement',
                    ModePageSponsoring::Generique => 'Sans évènement : page de sponsoring de l\'AFUP',
                },
            ])
            ->add('texteFr', TextareaType::class, [
                'label' => 'Texte (FR)',
                'attr' => ['rows' => 5, 'cols' => 50, 'class' => 'easymde'],
                'required' => false,
                'empty_data' => '',
            ])
            ->add('texteEn', TextareaType::class, [
                'label' => 'Texte (EN)',
                'attr' => ['rows' => 5, 'cols' => 50, 'class' => 'easymde'],
                'required' => false,
                'empty_data' => '',
            ])
            ->add('sponsor_file_fr', FileType::class, [
                'label' => 'Dossier de sponsoring (FR)',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Assert\File(mimeTypes: 'application/pdf'),
                ],
            ])
            ->add('sponsor_file_en', FileType::class, [
                'label' => 'Dossier de sponsoring (EN)',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new Assert\File(mimeTypes: 'application/pdf'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ParametrageSponsoring::class,
        ]);
    }
}
