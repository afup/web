<?php

declare(strict_types=1);


namespace AppBundle\Security;

use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\Repository\GithubUserRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class MyGithubAuthenticator extends SocialAuthenticator
{
    private ClientRegistry $clientRegistry;
    private GithubUserRepository $githubUserRepository;
    private RouterInterface $router;

    public function __construct(ClientRegistry $clientRegistry, GithubUserRepository $githubUserRepository, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->githubUserRepository = $githubUserRepository;
        $this->router = $router;
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getGithubClient());
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        /** @var GithubResourceOwner $githubUser */
        $githubUser = $this->getGithubClient()
            ->fetchUserFromToken($credentials);

        // 1) have they logged in with Github before? Easy!
        $user = $this->githubUserRepository->getOneBy(['githubId' => $githubUser->getId()]);

        if ($user === null) {
            $user = new GithubUser();
        }

        $githubUserDetails = $githubUser->toArray();
        $user
            ->setGithubId($githubUserDetails['id'])
            ->setName($githubUserDetails['name'])
            ->setLogin($githubUserDetails['login'])
            ->setCompany($githubUserDetails['company'])
            ->setProfileUrl($githubUserDetails['html_url'])
            ->setAvatarUrl($githubUserDetails['avatar_url'])
        ;
        $this->githubUserRepository->save($user);

        return $user;
    }

    private function getGithubClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry
            // "github_main" is the key used in config.yml
            ->getClient('github_main');
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse($request->getSession()->get('_security.github_secured_area.target_path'));
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse($this->router->generate('connection_github'));
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'connection_github_check';
    }
}
