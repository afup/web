<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Model\Repository\UserRepository;
use Exception;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserDeleteAction extends AbstractController
{
    use DbLoggerTrait;

    private UserRepository $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $user = $this->userRepository->get($request->query->get('id'));
        if (null === $user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        try {
            $this->userRepository->remove($user);
            $this->log('Suppression de la personne physique ' . $user->getId());
            $this->addFlash('notice', 'La personne physique a été supprimée');
        } catch (InvalidArgumentException $e) {
            $this->addFlash('error', $e->getMessage());
        } catch (Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression de la personne physique');
        }

        return $this->redirectToRoute('admin_members_user_list');
    }
}
