<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Association\Genre;
use AppBundle\Event\Entity\GithubUser;
use AppBundle\Event\Entity\Repository\GithubUserRepository;
use AppBundle\Event\Entity\Speaker;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpeakerType extends AbstractType
{
    public const string OPT_PHOTO_REQUIRED = 'photo_required';
    public const string OPT_USER_GITHUB = 'user_github';
    public const string OPT_PREVIOUS_SPEAKER = 'previous_speaker';

    public function __construct(private readonly GithubUserRepository $githubUserRepository) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('genre', EnumType::class, [
                'required' => false,
                'class' => Genre::class,
                'label' => 'Genre',
                'placeholder' => 'Ne se prononce pas',
            ])
            ->add('firstname', TextType::class)
            ->add('lastname', TextType::class)
            ->add('email', EmailType::class)
            ->add('company', TextType::class, ['required' => false])
            ->add('locality', TextType::class, ['required' => false, 'help' => 'Indiquer votre ville nous permet de mieux anticiper les déplacements'])
            ->add('phone_number', TextType::class, ['required' => false, 'property_path' => 'phoneNumber', 'label' => 'Phone', 'help' => 'Utilisé uniquement pour vous contacter avant et durant de l\'évènement.'])
            ->add('biography', TextareaType::class)
            ->add('twitter', TextType::class, ['required' => false])
            ->add('mastodon', UrlType::class, ['required' => false, 'help' => 'Exemple https://mastodon.online/@afup', 'default_protocol' => 'https'])
            ->add('linkedin', UrlType::class, ['required' => false, 'help' => 'Exemple https://www.linkedin.com/in/votrepseudo', 'default_protocol' => 'https'])
            ->add('bluesky', TextType::class, ['required' => false, 'help' => 'Exemple acme.bsky.social'])
        ;

        if (true === $options[self::OPT_USER_GITHUB]) {
            // il vaudrait mieux passer par une option référent ou "saisie dans le BO", mais le bug
            // a été vu lors du Forum PHP 2022, on est pragmatique et le corrige au plus vite.
            $builder
                ->add('referent_person', TextType::class, ['required' => false, 'property_path' => 'referentPerson', 'label' => 'Personne référente'])
                ->add('referent_person_email', TextType::class, ['required' => false, 'property_path' => 'referentPersonEmail', 'label' => 'Email personne référente'])
                ->add('github_user',
                    ChoiceType::class,
                    [
                        'property_path' => 'githubUser',
                        'label' => 'Utilisateur GitHub',
                        'required' => false,
                        'choice_label' => fn(GithubUser $user): string => $user->getLabel(),
                        'choice_value' => function ($choice) {
                            if ($choice instanceof GithubUser) {
                                return $choice->getId();
                            }
                            return $choice;
                        },
                        'choice_loader' => new CallbackChoiceLoader(fn(): array => $this->githubUserRepository->getAllOrderedByLogin()),
                    ],
                )
            ;
        }

        $builder
            ->add('photoFile', FileType::class, ['label' => 'Photo de profil', 'data_class' => null, 'required' => $options[self::OPT_PHOTO_REQUIRED]])
            ->add('save', SubmitType::class, ['label' => 'Sauvegarder'])
        ;

        // Nouveau profil : prérempli depuis celui de la dernière édition.
        $previousSpeaker = $options[self::OPT_PREVIOUS_SPEAKER];
        $builder->addEventListener(FormEvents::PRE_SET_DATA, static function (FormEvent $formEvent) use ($previousSpeaker): void {
            $speaker = $formEvent->getData();
            if (!$speaker instanceof Speaker || !$previousSpeaker instanceof Speaker) {
                return;
            }

            $speaker->genre = $previousSpeaker->genre;
            $speaker->firstname = $previousSpeaker->firstname;
            $speaker->lastname = $previousSpeaker->lastname;
            $speaker->email = $previousSpeaker->email;
            $speaker->company = $previousSpeaker->company;
            $speaker->locality = $previousSpeaker->locality;
            $speaker->phoneNumber = $previousSpeaker->phoneNumber;
            $speaker->biography = $previousSpeaker->biography;
            $speaker->twitter = $previousSpeaker->twitter;
            $speaker->mastodon = $previousSpeaker->mastodon;
            $speaker->linkedin = $previousSpeaker->linkedin;
            $speaker->bluesky = $previousSpeaker->bluesky;
            // La photo n'est pas reprise ici : son fichier est rangé par édition, SpeakerAction le copie à l'enregistrement.

            $formEvent->getForm()->add('isFromPreviousEvent', HiddenType::class, ['mapped' => false]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                self::OPT_PHOTO_REQUIRED => true,
                self::OPT_USER_GITHUB => false,
                self::OPT_PREVIOUS_SPEAKER => null,
            ])
            ->setAllowedTypes(self::OPT_PREVIOUS_SPEAKER, ['null', Speaker::class])
        ;
    }
}
