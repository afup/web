<?php


namespace AppBundle\Controller;


use AppBundle\Association\Form\CompanyMemberType;

class MemberShipController extends SiteBaseController
{
    public function becomeMemberAction()
    {
        return $this->render(':site:become_member.html.twig');
    }

    public function companyAction()
    {
        $subscribeForm = $this->createForm(CompanyMemberType::class);

        return $this->render(':site:adhesion_entreprise.html.twig', ['form' => $subscribeForm->createView()]);
    }
}
