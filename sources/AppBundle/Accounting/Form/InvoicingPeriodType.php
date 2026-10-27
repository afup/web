<?php

declare(strict_types=1);

namespace AppBundle\Accounting\Form;

use AppBundle\Accounting\Entity\Repository\InvoicingPeriodRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InvoicingPeriodType extends AbstractType
{
    public function __construct(private readonly InvoicingPeriodRepository $invoicingPeriodRepository) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $periods = [];
        foreach ($this->invoicingPeriodRepository->findAll() as $period) {
            $periods["{$period->dateDebut->format('d/m/Y')} - {$period->dateFin->format('d/m/Y')}"] = $period->id;
        }

        $builder->add('periodId', ChoiceType::class, [
            'label' => 'Année comptable',
            'required' => false,
            'choices' => $periods,
            'property_path' => 'id',
            'placeholder' => false,
            'attr' => [ 'onchange' => 'this.form.submit(); return false;'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['method' => 'GET', 'csrf_protection' => false]);
    }

    #[\Override]
    public function getBlockPrefix()
    {
        return '';
    }
}
