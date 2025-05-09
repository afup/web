<?php

declare(strict_types=1);


namespace AppBundle\Security;

use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\Repository\GithubUserRepository;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use League\OAuth2\Client\Provider\GithubResourceOwner;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class GithubAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    public function __construct(
        private readonly ClientRegistry $clientRegistry,
        private readonly GithubUserRepository $githubUserRepository,
        private readonly RouterInterface $router,
    ) {
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $client = $this->getGithubClient();
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client) {
                /** @var GithubResourceOwner $githubUser */
                $githubUser = $client->fetchUserFromToken($accessToken);

                // 1) have they logged in with GitHub before? Easy!
                $user = $this->githubUserRepository->getOneBy(['githubId' => $githubUser->getId()]);
                if ($user === null) {
                    $user = new GithubUser();
                }

                $user
                    ->setGithubId($githubUser->getId())
                    ->setName($githubUser->getName())
                    ->setLogin($githubUser->getNickname())
                ;
                $this->githubUserRepository->save($user);

                return $user;
            })
        );
    }

    private function getGithubClient(): OAuth2ClientInterface
    {
        // "github_main" is the key used in config.yml
        return $this->clientRegistry->getClient('github_main');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData()),
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $firewallName): RedirectResponse
    {
        return new RedirectResponse($request->getSession()->get('_security.github_secured_area.target_path'));
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        return new RedirectResponse($this->router->generate('connection_github'));
    }

    public function supports(Request $request): bool
    {
        return $request->attributes->get('_route') === 'connection_github_check';
    }
}
