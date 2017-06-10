<?php

namespace AppBundle\Event\Form;

use Afup\Site\Utils\Pays;
use AppBundle\Event\Model\Invoice;
use AppBundle\Event\Model\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PurchaseType extends AbstractType
{
    /**
     * @var Pays
     */
    private $country;

    private $tokenStorage;

    public function __construct(Pays $pays, TokenStorageInterface $tokenStorage)
    {
        $this->country = $pays;
        $this->tokenStorage = $tokenStorage;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tickets', CollectionType::class, [
                // each entry in the array will be an "email" field
                'entry_type' => TicketType::class,
                'prototype' => true,
                'allow_add'    => true,
                'entry_options' => [
                    'event_id' => $options['event_id'],
                    'member_type' => $options['member_type']
                ]
            ])
            ->add('paymentType', ChoiceType::class, [
                'label' => 'Règlement',
                'choices' => [
                    'Carte bancaire' => Ticket::PAYMENT_CREDIT_CARD,
                    'Virement' => Ticket::PAYMENT_BANKWIRE
                ],
                'expanded' => true,
                'multiple' => false
            ])
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('company', TextType::class, ['required' => false])
            ->add('email', EmailType::class)
            ->add('address', TextType::class)
            ->add('zipcode', TextType::class, ['label' => 'Zip code'])
            ->add('city', TextType::class)
            ->add('countryId', ChoiceType::class, [
                'label' => 'Country',
                'choices' => array_flip($this->country->obtenirPays())
            ])
            ->add('codeOfConduct', CheckboxType::class, [
                'label' => 'L\'ensemble des participants s\'engage à respecter le Code de conduite*', // @todo check label et ajouter le renvoi vers l'asterisque
                'required' => true,
                'mapped' => false
            ])
            /*->add('companyCitation', CheckboxType::class, [
                'label'    => 'Citer ma société en tant que participante à la conférence', // @todo check label
                'required' => false
            ])
            ->add('newsletterAfup', CheckboxType::class, [
                'label' => 'M\'abonner à la newsletter afup', // @todo check label
                'required' => false
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
            'member_type' => TicketType::MEMBER_NOT,
            'event_id' => null,
            'cascade_validation' => true,
            'validation_groups' => function () {
                $groups = ['Default'];

                if (is_object($this->tokenStorage->getToken()->getUser()) === false) {
                    $groups[] = 'not_logged_in';
                } elseif ((int)$this->tokenStorage->getToken()->getUser()->getCompanyId() === 0) {
                    $groups[] = 'personal';
                } else {
                    $groups[] = 'corporate';
                }

                return $groups;
            },
        ]);
    }
}
