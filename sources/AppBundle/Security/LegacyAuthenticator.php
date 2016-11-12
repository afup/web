<?php

namespace AppBundle\Security;

use AppBundle\Association\Model\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LegacyAuthenticator extends AbstractGuardAuthenticator
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        if (substr($request->getPathInfo(), 0, 7) !== '/admin/') {
            return null;
        }

        if ($request->hasSession() === false) {
            throw new \RuntimeException('Session must be started');
        }

        try {
            $user = $this->userRepository->loadUserByUsername($_SESSION['afup_login']);
        } catch (UsernameNotFoundException $e) {
            return null;
        }

        return ['id' => $user->getId(), 'password' => $user->getPassword()];
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $this->userRepository->getOneBy(['id' => $credentials['id'], 'password' => $credentials['password']]);
        if ($user === null) {
            throw new AuthenticationException(sprintf('Unknown user %s', $credentials['id']));
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        return ($credentials['id'] === $user->getId() && $credentials['password'] === $user->getPassword());
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response('Access denied', 403);
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function supportsRememberMe()
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse('/pages/administration/index.php');
    }
}
