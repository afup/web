<?php

declare(strict_types=1);


namespace AppBundle\Security;

use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\Repository\GithubUserRepository;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class TestGithubAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
{
    private GithubUserRepository $githubUserRepository;

    public function __construct(GithubUserRepository $githubUserRepository)
    {
        $this->githubUserRepository = $githubUserRepository;
    }

    public function authenticate(Request $request): SelfValidatingPassport
    {
        $githubUsers = $this->getTestUsersDetails();
        $userTest = $request->get('github_test_user');
        if (!isset($githubUsers[$userTest])) {
            throw new \RuntimeException('Test user not found');
        }
        $githubUserDetails = $githubUsers[$userTest];

        return new SelfValidatingPassport(
            new UserBadge($userTest, function () use ($githubUserDetails) {
                $user = $this->githubUserRepository->getOneBy(['githubId' => $githubUserDetails['id']]);

                if ($user === null) {
                    $user = new GithubUser();
                }

                $user
                    ->setGithubId($githubUserDetails['id'])
                    ->setName($githubUserDetails['name'])
                    ->setLogin($githubUserDetails['login']);
                $this->githubUserRepository->save($user);

                return $user;
            })
        );
    }

    private function getTestUsersDetails(): array
    {
        return ['userGithub1' => [
            'id' => 10,
            'name' => 'Name1',
            'login' => 'user_github_1',
        ], 'userGithub2' => [
            'id' => 42,
            'name' => 'Name2',
            'login' => 'user_github_2',
        ], 'agallou' => [
            'id' => 320372,
            'name' => 'agallou',
            'login' => 'agallou',
        ]];
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): JsonResponse
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $firewallName): RedirectResponse
    {
        return new RedirectResponse($request->getSession()->get('_security.github_secured_area.target_path'));
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        $body = "<h1>Oauth login test</h1>";
        foreach (array_keys($this->getTestUsersDetails()) as $name) {
            $uri = $request->getUri();
            $uri .= $request->query->count() ? '&' : '?';
            $uri .= 'github_test_user=' . $name;
            $body .= sprintf('<a href="%s">Connect as %s</a><br />', $uri, $name);
        }
        return new Response($body);
    }

    public function supports(Request $request): bool
    {
        return (bool) $request->get('github_test_user');
    }
}
