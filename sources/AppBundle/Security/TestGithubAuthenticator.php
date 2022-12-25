<?php


namespace AppBundle\Security;

use AppBundle\Event\Model\GithubUser;
use AppBundle\Event\Model\Repository\GithubUserRepository;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class TestGithubAuthenticator extends SocialAuthenticator
{
    private $githubUserRepository;
    private $router;

    public function __construct(GithubUserRepository $githubUserRepository, RouterInterface $router)
    {
        $this->githubUserRepository = $githubUserRepository;
        $this->router = $router;
    }

    public function getCredentials(Request $request)
    {
        return $request->get('github_test_user');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $githubUsers = $this->getTestUsersDetails();


        if (!isset($githubUsers[$credentials])) {
            throw new \RuntimeException('Test user not found');
        }

        $githubUserDetails = $githubUsers[$credentials];

        $user = $this->githubUserRepository->getOneBy(['githubId' => $githubUserDetails['id']]);

        if ($user === null) {
            $user = new GithubUser();
        }

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

    private function getTestUsersDetails()
    {
        $testUsers = [];

        $testUsers['userGithub1'] = [
            'id' => 10,
            'name' => 'Name1',
            'login' => 'user_github_1',
            'company' => null,
            'html_url' => 'http://test1.com',
            'avatar_url' => 'http://test1.com',
        ];

        $testUsers['userGithub2'] = [
            'id' => 42,
            'name' => 'Name2',
            'login' => 'user_github_2',
            'company' => 'company name',
            'html_url' => 'http://test2.com',
            'avatar_url' => 'http://test2.com',
        ];

        return $testUsers;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, 403);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $body = "<h1>Oauth login test</h1>";
        foreach ($this->getTestUsersDetails() as $name => $infos) {
            $uri = $request->getUri();
            $uri .= $request->query->count() ? '&' : '?';
            $uri .= 'github_test_user=' . $name;
            $body .= sprintf('<a href="%s">Connect as %s</a><br />', $uri, $name);
        }
        return new Response($body);
    }
}
