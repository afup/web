<?php

declare(strict_types=1);


namespace AppBundle\Association\Form;

use Afup\Site\Utils\Pays;
use AppBundle\Antennes\AntennesCollection;
use AppBundle\Association\Model\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactDetailsType extends AbstractType
{
    /**
     * @var Pays
     */
    public $countryService;

    public function __construct(Pays $countryService)
    {
        $this->countryService = $countryService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'constraints' => [
                    new Email(),
                ],
            ])
            ->add('address', TextareaType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Zip code',
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('city', TextType::class, [
                'constraints' => [
                    new NotBlank(),
                ],
            ])
            ->add('country', ChoiceType::class, [
                'label' => 'Pays',
                'choices' => $this->getCountyChoices(),
                'preferred_choices' => ['FR'],
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'constraints' => [
                    new Length(['max' => 20]),
                ],
            ])
            ->add('mobilephone', TextType::class, [
                'label' => 'Portable',
                'required' => false,
                'constraints' => [
                    new Length(['max' => 20]),
                ],
            ])
            ->add('nearest_office', ChoiceType::class, [
                'choices' => $this->getOfficesList(),
            ])
            ->add('username', TextType::class, [
                'attr' => [
                    'maxlength' => 30,
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length(['max' => 30]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'required' => false,
                'invalid_message' => 'The password fields must match',
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
            ])
            ->add('save', SubmitType::class, ['label' => 'Update'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    /**
     * @return mixed[]
     */
    private function getOfficesList(): array
    {
        $antennesCollection = new AntennesCollection();
        $offices = ['' => '-Aucune-'];
        foreach ($antennesCollection->getAllSortedByLabels() as $antenne) {
            $offices[$antenne->label] = $antenne->code;
        }
        return $offices;
    }

    /**
     * @return mixed[]
     */
    private function getCountyChoices(): array
    {
        $choices = [];
        foreach ($this->countryService->obtenirPays() as $id => $country) {
            $choices[$country] = $id;
        }
        return $choices;
    }
}
