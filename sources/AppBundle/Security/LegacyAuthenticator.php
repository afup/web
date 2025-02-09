<?php

declare(strict_types=1);

namespace AppBundle\Security;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LegacyAuthenticator extends AbstractGuardAuthenticator
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request)
    {
        return $request->getPathInfo() === '/admin/login'
            && $request->isMethod(Request::METHOD_POST)
            && $request->request->has('utilisateur')
            && $request->request->has('mot_de_passe');
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        return [
            'login' => $request->request->get('utilisateur'),
            'password' => md5($request->request->get('mot_de_passe')),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $this->userRepository->loadUserByUsername($credentials['login']);

        if ($user === null) {
            throw new AuthenticationException(sprintf('Unknown user %s', $credentials['login']));
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        return (
            ($credentials['login'] === $user->getUsername() || $credentials['login'] === $user->getEmail())
            && $credentials['password'] === $user->getPassword()
        );
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        /** @var SessionInterface&Session $session */
        $session = $request->getSession();
        $session->getFlashBag()->add('error', "Utilisateur et/ou mot de passe incorrect");

        return null;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        $actualUrl = $request->getSchemeAndHttpHost() . $request->getRequestUri();
        $targetPath = $request->request->get('_target_path');
        if ($targetPath !== $actualUrl && $targetPath && parse_url($targetPath, PHP_URL_HOST) === null) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse('/member/');
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
        if ($request->server->has('LEGACY_REFERER')) {
            return new RedirectResponse(sprintf('/admin/login?target=%s', urlencode($request->server->get('LEGACY_REFERER'))));
        }

        return new RedirectResponse('/admin/login');
    }
}
