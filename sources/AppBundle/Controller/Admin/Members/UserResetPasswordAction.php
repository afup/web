<?php

namespace AppBundle\Controller\Admin\Members;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Association\UserMembership\UserService;
use Exception;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserResetPasswordAction
{
    use DbLoggerTrait;

    /** @var UserRepository */
    private $userRepository;
    /** @var UserService */
    private $userPasswordService;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        UserRepository $userRepository,
        UserService $userPasswordService,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->userRepository = $userRepository;
        $this->userPasswordService = $userPasswordService;
        $this->flashBag = $flashBag;
        $this->urlGenerator = $urlGenerator;
    }

    public function __invoke(Request $request)
    {
        $user = $this->userRepository->get($request->query->get('id'));
        if (null === $user) {
            throw new NotFoundHttpException('Utilisateur non trouvé');
        }
        try {
            $this->userPasswordService->resetPassword($user);
            $this->log('Envoi d\'un nouveau mot de passe à la personne physique ' . $user->getId());
            $this->flashBag->add('notice', 'Un nouveau mot de passe a été envoyé à la personne physique');
        } catch (Exception $e) {
            $this->flashBag->add('error', 'Une erreur est survenue lors de l\'envoi d\'un nouveau mot de passe à la personne physique');
        }

        return new RedirectResponse($this->urlGenerator->generate('admin_members_user_list'));
    }
}
