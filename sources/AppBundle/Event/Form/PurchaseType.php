<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use Afup\Site\Utils\Pays;
use AppBundle\Association\Model\User;
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
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Validator\Constraints\IsTrue;

class PurchaseType extends AbstractType
{
    const MAX_NB_PERSONNES = 15;

    public function __construct(
        private readonly Pays $country,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $maxNbPersonne = $options['special_price_token'] ? 1 : self::MAX_NB_PERSONNES;

        $nbPersonnesChoices = [];
        for ($i=1; $i<=$maxNbPersonne; $i++) {
            $nbPersonnesChoices[$i] = $i;
        }

        $builder
            ->add('nbPersonnes', ChoiceType::class, [
                'choices' => $nbPersonnesChoices,
                'multiple' => false,
                'expanded' => false,
                'mapped' => false,
                'data' => 1,
            ])
            ->add('tickets', CollectionType::class, [
                'entry_type' => TicketType::class,
                'prototype' => true,
                'allow_add'    => true,
                'entry_options' => [
                    'event_id' => $options['event_id'],
                    'member_type' => $options['member_type'],
                    'is_cfp_submitter' => $options['is_cfp_submitter'],
                    'special_price_token' => $options['special_price_token'],
                    'error_bubbling' => false,
                ],
            ])
            ->add('paymentType', ChoiceType::class, [
                'label' => 'Règlement',
                'choices' => [
                    'Carte bancaire' => Ticket::PAYMENT_CREDIT_CARD,
                    'Virement' => Ticket::PAYMENT_BANKWIRE,
                ],
                'expanded' => true,
                'multiple' => false,
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
                'choices' => array_flip($this->country->obtenirPays()),
            ])
            ->add('companyCitation', CheckboxType::class, [
                'label'    => 'J\'accepte que ma société soit citée comme participant à la conférence',
                'required' => false,
                'mapped' => false,
            ])
            ->add('cgv', CheckboxType::class, [
                'required' => true,
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous devez valider les conditions.',
                    ]),
                ],
            ])
            ->add('newsletterAfup', CheckboxType::class, [
                'label' => 'Je souhaite être tenu au courant des rencontres de l\'AFUP sur des sujets afférents à PHP',
                'required' => false,
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Invoice::class,
            'member_type' => TicketType::MEMBER_NOT,
            'is_cfp_submitter' => false,
            'special_price_token' => null,
            'event_id' => null,
            'cascade_validation' => true,
            'validation_groups' => function (): array {
                $groups = ['Default'];

                $user = null;
                if ($this->tokenStorage->getToken() instanceof TokenInterface) {
                    $user = $this->tokenStorage->getToken()->getUser();
                }

                if ($user === null) {
                    $groups[] = 'not_logged_in';
                } elseif ($user instanceof User && (int) $user->getCompanyId() === 0) {
                    $groups[] = 'personal';
                } else {
                    $groups[] = 'corporate';
                }

                return $groups;
            },
        ]);
    }
}
