<?php

namespace AppBundle\Controller\Admin\Members;

use Afup\Site\Logger\DbLoggerTrait;
use AppBundle\Association\Form\UserBadgeType;
use AppBundle\Association\Form\UserEditFormDataFactory;
use AppBundle\Association\Form\UserEditType;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Event\Model\Repository\UserBadgeRepository;
use Assert\Assertion;
use Exception;
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

    /** @var UserRepository */
    private $userRepository;
    /** @var UserBadgeRepository */
    private $userBadgeRepository;
    /** @var UserEditFormDataFactory */
    private $userEditFormDataFactory;
    /** @var FormFactoryInterface */
    private $formFactory;
    /** @var UrlGeneratorInterface */
    private $urlGenerator;
    /** @var FlashBagInterface */
    private $flashBag;
    /** @var Environment */
    private $twig;

    public function __construct(
        UserRepository $userRepository,
        UserBadgeRepository $userBadgeRepository,
        UserEditFormDataFactory $userEditFormDataFactory,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        FlashBagInterface $flashBag,
        Environment $twig
    ) {
        $this->userRepository = $userRepository;
        $this->userBadgeRepository = $userBadgeRepository;
        $this->userEditFormDataFactory = $userEditFormDataFactory;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->flashBag = $flashBag;
        $this->twig = $twig;
    }

    public function __invoke(Request $request)
    {
        $user = $this->userRepository->get($request->query->get('id'));
        Assertion::notNull($user);
        $data = $this->userEditFormDataFactory->fromUser($user);
        $form = $this->formFactory->create(UserEditType::class, $data);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->userEditFormDataFactory->toUser($data, $user);
            try {
                $this->userRepository->edit($user);
                $this->log('Modification de la personne physique ' . $user->getFirstName() . ' ' . $user->getLastName() . ' (' . $user->getId() . ')');
                // Redirection sur la liste filtrée
                $this->flashBag->add('notice', 'La personne physique a été modifiée');

                return new RedirectResponse($this->urlGenerator->generate('admin_members_user_list', ['filter' => $user->getEmail()]));
            } catch (Exception $e) {
                $this->flashBag->add('error', 'Une erreur est survenue lors de la modification de la personne physique');
            }
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
