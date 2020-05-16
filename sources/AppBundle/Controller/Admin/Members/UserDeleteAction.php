<?php

namespace AppBundle\Controller\Admin\Members;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Model\Repository\UserRepository;
use Exception;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserDeleteAction
{
    use DbLoggerTrait;

    /** @var UserRepository */
    private $userRepository;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    public function __construct(
        UserRepository $userRepository,
        FlashBagInterface $flashBag,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->userRepository = $userRepository;
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
            $this->userRepository->remove($user);
            $this->log('Suppression de la personne physique ' . $user->getId());
            $this->flashBag->add('notice', 'La personne physique a été supprimée');
        } catch (InvalidArgumentException $e) {
            $this->flashBag->add('error', $e->getMessage());
        } catch (Exception $e) {
            $this->flashBag->add('error', 'Une erreur est survenue lors de la suppression de la personne physique');
        }

        return new RedirectResponse($this->urlGenerator->generate('admin_members_user_list'));
    }
}
