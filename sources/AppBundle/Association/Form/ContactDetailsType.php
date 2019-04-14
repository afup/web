<?php


namespace AppBundle\Association\Form;

use Afup\Site\Utils\Pays;
use AppBundle\Association\Model\User;
use AppBundle\Offices\OfficesCollection;
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

class ContactDetailsType extends AbstractType
{
    const LABEL_CLASS = 'libelle';

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = $options['data'];
        $builder
            ->add('email', EmailType::class, [
                'label_attr'=> [
                    'class' => self::LABEL_CLASS
                ]
            ])
            ->add('address', TextareaType::class, [
                'label_attr'=> [
                    'class' => self::LABEL_CLASS
                ]
            ])
            ->add('zipcode', TextType::class, [
                'label' => 'Zip code',
                'label_attr'=> [
                    'class' => self::LABEL_CLASS
                ]
            ])
            ->add('city', TextType::class, [
                'label_attr'=> [
                    'class' => self::LABEL_CLASS
                ]
            ])
            ->add('country', ChoiceType::class, [
                'choices' => $this->getCountyList(),
                'label_attr'=> [
                   'class' => self::LABEL_CLASS
                ]
            ])
            ->add('phone', TextType::class, [
                'required' => false,
                'label_attr'=> [
                    'class' => self::LABEL_CLASS
                ]
            ])
            ->add('mobilephone', TextType::class, [
                'label' => 'Mobile phone',
                'required' => false,
                'label_attr'=> [
                    'class' => self::LABEL_CLASS
                ]])
            ->add('nearest_office', ChoiceType::class, [
                'label' => 'Nearest office',
                'choices' => $this->getOfficesList($data->getNearestOffice()),
                'label_attr'=> [
                    'class' => self::LABEL_CLASS
                ]
            ])
            ->add('username', TextType::class, [
                'label' => 'Login',
                'attr' => [
                    'maxlength' => 30
                ],
                'label_attr'=> [
                    'class' => self::LABEL_CLASS
                ]
            ])
            ->add('password', RepeatedType::class, [
                'first_options'  => [
                    'label' => 'Password',
                    'label_attr' => [
                        'class' => self::LABEL_CLASS
                    ]
                ],
                'second_options' => [
                    'label' => 'Repeat Password',
                    'label_attr' => [
                        'class' => self::LABEL_CLASS
                    ]
                ],
                'type' => PasswordType::class,
                'required' => false,
                'invalid_message' => 'The password fields must match'
            ])
            ->add('save', SubmitType::class, ['label' => 'Modify'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }

    private function getOfficesList($nearestOffice)
    {
        $officesCollection = new OfficesCollection();
        $offices = ['' => '-Aucune-'];
        foreach ($officesCollection->getOrderedLabelsByKey() as $id => $city) {
            $offices[$city] = $id;
        }
        return $offices;
    }

    private function getCountyList()
    {
        global $bdd;
        $pays = new Pays($bdd);
        return  $pays->obtenirPays();
    }
}
