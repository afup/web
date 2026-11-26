<?php

declare(strict_types=1);

namespace AppBundle\Antennes\Form;

use AppBundle\Antennes\Entity\Antenne;
use AppBundle\Antennes\Entity\Repository\AntenneRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<AntenneFormData>
 */
final class AntenneType extends AbstractType
{
    public function __construct(private readonly AntenneRepository $antenneRepository) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['creation']) {
            $builder->add('code', TextType::class, [
                'label' => 'Code',
                'help' => 'Identifiant technique (ex. lyon) : commence par une lettre ; lettres minuscules, chiffres et underscores. Il ne pourra plus être modifié.',
            ]);
        }

        $builder
            ->add('libelle', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('urlLogo', UrlType::class, [
                'label' => 'Logo',
                'default_protocol' => 'https',
                'help' => 'URL du logo (ex. hébergé sur https://identity.afup.org/) ou chemin commençant par « / »',
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'Active',
                'help' => "Une antenne inactive est masquée de la page des antennes, de l'API, des listes de choix (antenne la plus proche, filtre des talks) et du formulaire Super Apéro : son meetup Super Apéro sera supprimé à la prochaine modification de celui-ci. Elle peut toutefois encore être attribuée automatiquement comme antenne la plus proche d'un membre.",
                'required' => false,
            ])
            ->add('meetupNomUrl', TextType::class, [
                'label' => "Nom d'URL Meetup",
                'help' => 'Ex. afup-lyon-php pour https://www.meetup.com/afup-lyon-php/',
                'required' => false,
            ])
            ->add('meetupIdExterne', TextType::class, [
                'label' => 'Identifiant Meetup',
                'help' => 'Identifiant numérique du groupe Meetup',
                'required' => false,
            ])
            ->add('reseauxSociaux', ReseauxSociauxType::class, [
                'label' => false,
            ])
            ->add('region', TextType::class, [
                'label' => 'Région',
                'required' => false,
            ])
            ->add('departements', TextType::class, [
                'label' => 'Départements',
                'help' => 'Numéros séparés par des virgules (ex. 69, 01)',
                'required' => false,
            ])
            ->add('pays', TextType::class, [
                'label' => 'Pays',
                'help' => 'Codes séparés par des virgules, pour les antennes hors de France (ex. luxembourg)',
                'required' => false,
            ])
            ->add('carte', CarteType::class, [
                'label' => false,
            ]);

        $listeTransformer = new CallbackTransformer(self::listeVersTexte(...), self::texteVersListe(...));

        $builder->get('departements')->addModelTransformer($listeTransformer);
        $builder->get('pays')->addModelTransformer($listeTransformer);

        if ($options['creation']) {
            $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event): void {
                /** @var AntenneFormData $data */
                $data = $event->getData();

                if ($data->code === null || $data->code === '') {
                    return;
                }

                if ($this->antenneRepository->find($data->code) instanceof Antenne) {
                    $event->getForm()->get('code')->addError(
                        new FormError("Une antenne existe déjà avec le code « {$data->code} »."),
                    );
                }
            });
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AntenneFormData::class,
            'creation' => false,
            'validation_groups' => fn(Options $options): array => $options['creation']
                ? ['Default', AntenneFormData::GROUPE_CREATION]
                : ['Default'],
        ]);
        $resolver->setAllowedTypes('creation', 'bool');
    }

    /**
     * @param string[]|null $valeurs
     */
    private static function listeVersTexte(?array $valeurs): string
    {
        return implode(', ', $valeurs ?? []);
    }

    /**
     * @return string[]|null
     */
    private static function texteVersListe(?string $texte): ?array
    {
        $valeurs = array_values(array_filter(
            array_map(trim(...), explode(',', (string) $texte)),
            fn(string $valeur): bool => $valeur !== '',
        ));

        return $valeurs === [] ? null : $valeurs;
    }
}
