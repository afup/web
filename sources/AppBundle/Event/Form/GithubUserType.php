<?php

declare(strict_types=1);

namespace AppBundle\Event\Form;

use AppBundle\Event\Entity\GithubUser;
use AppBundle\Github\Exception\UnableToFindGithubUserException;
use AppBundle\Github\Exception\UnableToGetGithubUserInfosException;
use AppBundle\Github\GithubClient;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GithubUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', TextType::class, [
                'constraints' => [
                    new UniqueEntity(
                        fields: ['login'],
                        message: 'Another entity exists for this data: {{ value }}',
                        entityClass: GithubUser::class,
                    ),
                ],
                'invalid_message' => 'Impossible de charger les informations de l\'utilisateur GitHub.',
            ])
            ->add('afupCrew', CheckboxType::class, [
                'required' => false,
            ])
            ->add('save', SubmitType::class, ['label' => 'Sauvegarder'])
        ;

        /** @var GithubClient $githubClient */
        $githubClient = $options['github_client'];

        $builder->get('user')
            ->addModelTransformer(new CallbackTransformer(
                /**
                 * @param $githubUser null|GithubUser
                 */
                fn($githubUser) => $githubUser === null ? null : $githubUser->getLogin(),
                /**
                 * @param $githubUsername string
                 */
                function ($githubUsername) use ($githubClient) {
                    if ($githubUsername === null) {
                        return null;
                    }

                    try {
                        return $githubClient->getUserInfos($githubUsername);
                    } catch (UnableToFindGithubUserException|UnableToGetGithubUserInfosException $e) {
                        throw new TransformationFailedException($e->getMessage());
                    }
                },
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => GithubUserFormData::class,
                'github_client' => null,
            ])
            ->setAllowedTypes('github_client', GithubClient::class)
            ->setRequired(['github_client'])
        ;
    }
}
