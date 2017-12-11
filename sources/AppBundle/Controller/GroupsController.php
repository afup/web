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
        $lists = $this->get('app.mailing_list_repository')->getAllMailingLists();
        $groupRepository = $this->get('app.group_repository');

        $subscriptions = [];

        foreach ($lists as $list)
        {
            $subscriptions[$list->getEmail()] = $groupRepository->hasMember($list->getEmail(), $this->getUser()->getEmail());
        }

        /*$groupClient = $this->get('app.group_client');

        dump($groupClient->members->get('test-gestion-auto@afup.org', 'xavier.leune@gmail.com'));
        try {
            dump($groupClient->members->get('test-gestion-auto@afup.org', 'bureau@afup.org'));
        } catch (\Google_Service_Exception $exception) {
            if ($exception->getCode() !== 404) {
                throw $exception;
            } else {
                dump("not a member");
            }
        }
        dump($groupClient->members->listMembers('test-gestion-auto@afup.org')->getMembers());*/
        /*$member = new \Google_Service_Directory_Member();
        $member->setEmail('xavier.leune@gmail.com');
        dump($groupClient->members->insert('test-gestion-auto@afup.org', $member));*/
        return $this->render('admin/groups/lists.html.twig', [
            'csrf_token' => $token,
            'lists' => $lists,
            'subscriptions' => $subscriptions,
            'title' => 'Mes listes de diffusion'
        ]);
    }

    public function registerGroupAction(Request $request)
    {
        $csrf = $this->get('security.csrf.token_manager')->getToken('GroupsAction');

        if ($csrf->getValue() !== $request->get('token')) {
            $this->addFlash('error', 'Jeton anti-csrf invalide.');
            goto redirect;
        }

        $email = $this->getUser()->getEmail();
        $groupRepository = $this->get('app.group_repository');
        $mailingListRepository = $this->get('app.mailing_list_repository');
        if ($request->request->get('subscribe') !== null) {
            $mailingId = $request->request->getInt('subscribe');
            /**
             * @var $mailing MailingList
             */
            $mailing = $mailingListRepository->get($mailingId);

            if ($groupRepository->addMember($mailing->getEmail(), $email)) {
                $this->addFlash('info', sprintf('Vous avez été abonné à la liste "%s"', $mailing->getName()));
            } else {
                $this->addFlash('error', 'Une erreur est survenue lors de la prise en compte de votre abonnement.');
            }
        } elseif($request->request->get('unsubscribe') !== null) {
            $mailingId = $request->request->getInt('unsubscribe');
            /**
             * @var $mailing MailingList
             */
            $mailing = $mailingListRepository->get($mailingId);

            if ($groupRepository->removeMember($mailing->getEmail(), $email)) {
                $this->addFlash('info', sprintf('Vous avez été désabonné de la liste "%s"', $mailing->getName()));
            } else {
                $this->addFlash('error', 'Une erreur est survenue lors de la prise en compte de votre désabonnement.');
            }
        } else {
            $this->addFlash('error', 'Impossible vous abonner à cette liste');
        }

        redirect:;
        return $this->redirectToRoute('my_groups');
    }
}
