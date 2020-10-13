<?php

namespace App\RendezVous\Admin\PrepareRendezVous;

use AppBundle\Offices\OfficesCollection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PrepareRendezVousFormType extends AbstractType
{
    /** @var OfficesCollection */
    private $officeRepository;

    public function __construct(OfficesCollection $officeRepository)
    {

        $this->officeRepository = $officeRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'attr' => [
                    'size' => 50,
                    'maxlength' => 255,
                ],
            ])
            ->add('pitch', TextareaType::class, [
                'label' => 'Accroche',
                'required' => true,
                'attr' => [
                    'cols' => 42,
                    'rows' => 10,
                ],
            ])
            ->add('theme', TextareaType::class, [
                'label' => 'Thème',
                'required' => true,
                'attr' => [
                    'cols' => 42,
                    'rows' => 10,
                ],
            ])
            ->add('officeId', ChoiceType::class, [
                'label' => 'Antenne',
                'required' => true,
                'choices_as_values' => true,
                'choices' => array_flip($this->officeRepository->getOrderedLabelsByKey()),
            ])
            ->add('date', DateType::class, [
                'label' => 'Date',
                'required' => true,
            ])
            ->add('start', TextType::class, [
                'label' => 'Heure début (00:00)',
                'required' => true,
                'attr' => ['size' => 6, 'maxlength' => 5],
            ])
            ->add('end', TextType::class, [
                'label' => 'Heure fin (00:00)',
                'required' => true,
                'attr' => ['size' => 6, 'maxlength' => 5],
            ])
            ->add('place', TextareaType::class, [
                'label' => 'Lieu',
                'required' => true,
                'attr' => [
                    'cols' => 42,
                    'rows' => 10,
                ],
            ])
            ->add('url', TextType::class, [
                'label' => 'URL',
                'required' => true,
                'attr' => [
                    'size' => 42,
                ],
            ])
            ->add('address', TextareaType::class, [
                'label' => 'Adresse',
                'required' => true,
                'attr' => [
                    'cols' => 42,
                    'rows' => 10,
                ],
            ])
            ->add('plan', TextType::class, [
                'label' => 'Plan',
                'required' => true,
                'attr' => [
                    'size' => 42,
                ],
            ])
            ->add('capacity', NumberType::class, [
                'label' => 'Capacité',
                'required' => true,
                'attr' => [
                    'size' => 6,
                    'maxlength' => 5,
                ],
            ])
            ->add('registration', ChoiceType::class, [
                'label' => 'L\'inscription est gérée par le back-office de l\'AFUP',
                'required' => true,
                'expanded' => true,
                'choices_as_values' => true,
                'choices' => [
                    'Oui' => 1,
                    'Non' => 0,
                ],
            ])
            ->add('externalUrl', TextType::class, [
                'label' => 'Si non, saisir l\'URL d\'enregistrement (possible ne rien remplir)',
                'required' => false,
                'attr' => [
                    'size' => 42,
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Envoyer']);

        for ($i = 1; $i <= 4; $i++) {
            $builder->add('slide'.$i.'Url', TextType::class, ['required' => false]);
            $builder->add('slide'.$i, FileType::class, ['required' => false]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PrepareRendezVousFormData::class,
        ]);
    }
}
