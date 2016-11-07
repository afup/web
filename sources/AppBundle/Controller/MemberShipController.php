<?php


namespace AppBundle\Controller;


class MemberShipController extends SiteBaseController
{
    public function becomeMemberAction()
    {
        return $this->render(':site:become_member.html.twig');
    }
}
