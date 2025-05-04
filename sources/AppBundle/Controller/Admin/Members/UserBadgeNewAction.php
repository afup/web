<?php

declare(strict_types=1);

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Association\Form\UserBadgeType;
use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Event\Model\Repository\UserBadgeRepository;
use AppBundle\Event\Model\UserBadge;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class UserBadgeNewAction
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly FormFactoryInterface $formFactory,
        private readonly UserBadgeRepository $userBadgeRepository,
    ) {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $user = $this->userRepository->get($request->get('user_id'));
        $userBadgeForm = $this->formFactory->create(UserBadgeType::class, [], ['user' => $user]);
        $userBadgeForm->handleRequest($request);
        $data = $userBadgeForm->getData();

        $userBadge = new UserBadge();
        $userBadge->setBadgeId($data['badge']);
        $userBadge->setIssuedAt($data['date']);
        $userBadge->setUserId($data['user']);
        $this->userBadgeRepository->save($userBadge);

        return new RedirectResponse($request->headers->get('referer'));
    }
}
