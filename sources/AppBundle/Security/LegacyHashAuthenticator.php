<?php

declare(strict_types=1);

namespace AppBundle\Security;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\Model\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

class LegacyHashAuthenticator extends AbstractGuardAuthenticator
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request)
    {
        return $request->query->has('hash') && $request->isMethod(Request::METHOD_GET);
    }

    /**
     * @inheritDoc
     */
    public function getCredentials(Request $request)
    {
        return ['hash' => $request->query->get('hash')];
    }

    /**
     * @inheritDoc
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $user = $this->userRepository->loadUserByHash($credentials['hash']);

        if ($user === null) {
            throw new AuthenticationException(sprintf('Unknown user %s', $credentials['hash']));
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

        return ($user->getHash() === $credentials['hash']);
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
        if ($request->server->has('LEGACY_REFERER') === true) {
            $newUrl = preg_replace('/(\?|&)hash=.+?(?:&|$)/', '$1', $request->server->get('LEGACY_REFERER'));
            $response = new RedirectResponse($newUrl, Response::HTTP_TEMPORARY_REDIRECT);
            $response->setPrivate();

            return $response;
        }

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
        return null;
    }
}
