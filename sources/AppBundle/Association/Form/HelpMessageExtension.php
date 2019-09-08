<?php

namespace AppBundle\Association\Form;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HelpMessageExtension extends AbstractTypeExtension
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['help'] = isset($options['help']) ? $options['help'] : '';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['help' => null]);
    }

    public function getExtendedType()
    {
        return FormType::class;
    }
}
