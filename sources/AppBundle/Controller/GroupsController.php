<?php

namespace AppBundle\Controller;

use AppBundle\Groups\Model\MailingList;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class GroupsController extends Controller
{
    public function myGroupsAction(Request $request)
    {
        if ($request->getMethod() === Request::METHOD_POST) {
            return $this->forward('AppBundle:Groups:registerGroup');
        }

        $token = $this->get('security.csrf.token_manager')->getToken('GroupsAction');

        /**
         * @var $lists MailingList[]
         */
        $lists = $this->get(\AppBundle\Groups\Model\Repository\MailingListRepository::class)->getAllMailingLists();
        $groupRepository = $this->get(\AppBundle\Groups\GroupRepository::class);

        $subscriptions = [];

        $error = null;

        try {
            foreach ($lists as $list) {
                $subscriptions[$list->getEmail()] = $groupRepository->hasMember($list->getEmail(), $this->getUser()->getEmail());
            }
        } catch (\Exception $exception) { // Can be a guzzle exception or google exception, does not matter actually
            $error = 'Une erreur est survenue en vérifiant les listes auxquelles vous etes abonnés. Les résultats ci-dessous peuvent ne pas refléter vos abonnements.';
        }

        return $this->render('admin/groups/lists.html.twig', [
            'csrf_token' => $token,
            'lists' => $lists,
            'error' => $error,
            'subscriptions' => $subscriptions,
            'title' => 'Mes listes de diffusion',
            'page' => 'groups'
        ]);
    }

    public function registerGroupAction(Request $request)
    {
        $csrf = $this->get('security.csrf.token_manager')->getToken('GroupsAction');

        if ($csrf->getValue() !== $request->get('token')) {
            $this->addFlash('error', 'Jeton anti-csrf invalide.');
            return $this->redirectToRoute('my_groups');
        }

        $email = $this->getUser()->getEmail();
        $groupRepository = $this->get(\AppBundle\Groups\GroupRepository::class);
        $mailingListRepository = $this->get(\AppBundle\Groups\Model\Repository\MailingListRepository::class);
        if ($request->request->get('subscribe') !== null) {
            $mailingId = $request->request->getInt('subscribe');
            /**
             * @var $mailing MailingList
             */
            $mailing = $mailingListRepository->get($mailingId);

            if ($groupRepository->addMember($mailing->getEmail(), $email)) {
                $this->addFlash('success', sprintf('Vous avez été abonné à la liste "%s"', $mailing->getName()));
            } else {
                $this->addFlash('error', 'Une erreur est survenue lors de la prise en compte de votre abonnement.');
            }
        } elseif ($request->request->get('unsubscribe') !== null) {
            $mailingId = $request->request->getInt('unsubscribe');
            /**
             * @var $mailing MailingList
             */
            $mailing = $mailingListRepository->get($mailingId);

            if ($groupRepository->removeMember($mailing->getEmail(), $email)) {
                $this->addFlash('success', sprintf('Vous avez été désabonné de la liste "%s"', $mailing->getName()));
            } else {
                $this->addFlash('error', 'Une erreur est survenue lors de la prise en compte de votre désabonnement.');
            }
        } else {
            $this->addFlash('error', 'Impossible vous abonner à cette liste');
        }

        return $this->redirectToRoute('my_groups');
    }
}
