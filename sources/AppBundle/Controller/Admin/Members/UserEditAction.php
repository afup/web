<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Form\UserBadgeType;
use AppBundle\Association\Form\UserEditType;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Event\Model\Repository\UserBadgeRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class UserEditAction
{
    use DbLoggerTrait;

    private UserRepository $userRepository;
    private UserBadgeRepository $userBadgeRepository;
    private FormFactoryInterface $formFactory;
    private UrlGeneratorInterface $urlGenerator;
    private FlashBagInterface $flashBag;
    private Environment $twig;

    public function __construct(
        UserRepository $userRepository,
        UserBadgeRepository $userBadgeRepository,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        Environment $twig
    ) {
        $this->userRepository = $userRepository;
        $this->userBadgeRepository = $userBadgeRepository;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $user = $this->userRepository->get($request->query->get('id'));
        if (!$user) {
            $this->flashBag->add('error', 'Utilisateur non trouvé');
            return new RedirectResponse($this->urlGenerator->generate('admin_members_user_list'));
        }
        $form = $this->formFactory->create(UserEditType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Save password if not empty
            $newPassword = $request->request->get($form->getName())['plainPassword']['first'];
            if ($newPassword) {
                $user->setPlainPassword($newPassword);
            }
            $this->userRepository->edit($user);
            $this->log('Modification de la personne physique ' . $user->getFirstName() . ' ' . $user->getLastName() . ' (' . $user->getId() . ')');
            // Redirection sur la liste filtrée
            $this->flashBag->add('notice', 'La personne physique a été modifiée');

            return new RedirectResponse($this->urlGenerator->generate('admin_members_user_list', ['filter' => $user->getEmail()]));
        }

        $userBadges = iterator_to_array($this->userBadgeRepository->findByUserId($user->getId()));
        $userBadgeForm = $this->formFactory->create(UserBadgeType::class, [], [
            'user' => $user,
            'action' => $this->urlGenerator->generate('admin_members_user_badge_new', ['user_id' => $user->getId()]),
        ]);

        return new Response($this->twig->render('admin/members/user_edit.html.twig', [
            'user' => $user,
            'user_badges' => $userBadges,
            'user_badge_form' => $userBadgeForm->createView(),
            'form' => $form->createView(),
        ]));
    }
}
