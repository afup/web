<?php

namespace AppBundle\Controller\Admin\Members;

use AppBundle\Event\Model\Repository\UserBadgeRepository;
use AppBundle\Event\Model\UserBadge;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserBadgeController extends Controller
{
    public function newAction(Request $request)
    {
        $userBadgeForm = $this->createForm(
            \AppBundle\Association\Form\UserBadgeType::class,
            [],
            ['user' => $this->getUser()]
        );

        $userBadgeForm->handleRequest($request);

        $data = $userBadgeForm->getData();

        $userBadge = new UserBadge();
        $userBadge->setBadgeId($data['badge']);
        $userBadge->setIssuedAt($data['date']);
        $userBadge->setUserId($data['user']);

        $userBadgeRepository = $this->get('ting')->get(UserBadgeRepository::class);
        $userBadgeRepository->save($userBadge);

        return $this->redirect($request->headers->get('referer'));
    }

    public function deleteAction(Request $request, $userId, $badgeId)
    {
        $userBadgeRepository = $this->get('ting')->get(UserBadgeRepository::class);
        $userBadge = $userBadgeRepository->getOneBy(['badgeId' => $badgeId, 'userId' => $userId]);
        $userBadgeRepository->delete($userBadge);

        return $this->redirect($request->headers->get('referer'));
    }
}
