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
            ->add('email', EmailType::class)
            ->add('address', TextareaType::class)
            ->add('zipcode', TextType::class)
            ->add('city', TextType::class)
            ->add('country', ChoiceType::class, [
                'choices' => $this->getCountyList()
            ])
            ->add('phone', TextType::class, [
                'required' => false
            ])
            ->add('mobilephone', TextType::class, [
                'required' => false
            ])
            ->add('nearest_office', ChoiceType::class, [
                'choices' => $this->getOfficesList($data->getNearestOffice()),
            ])
            ->add('username', TextType::class, [
                'attr' => [
                    'maxlength' => 30
                ]
            ])
            ->add('password', RepeatedType::class, [
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
