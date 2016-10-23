<?php


namespace AppBundle\Security;


use AppBundle\Model\GithubUser;
use AppBundle\Model\Repository\GithubUserRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\Provider\GithubClient;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class MyGithubAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $githubUserRepository;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, GithubUserRepository $githubUserRepository, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->githubUserRepository = $githubUserRepository;
        $this->router = $router;
    }

    public function getCredentials(Request $request)
    {
        if ($request->attributes->get('_route') !== 'connection_github_check') {
            // don't auth
            return;
        }

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

    /**
     * @return GithubClient
     */
    private function getGithubClient()
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
        $data = array(
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        );

        return new JsonResponse($data, 403);
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

}