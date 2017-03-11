<?php


namespace AppBundle\Controller;

class StaticController extends SiteBaseController
{
    public function officesAction()
    {
        return $this->render(':site:offices.html.twig');
    }

    public function superAperoAction()
    {
        return $this->render(':site:superapero.html.twig');
    }

    public function superAperoLiveAction()
    {
        return $this->render(':site:superapero_live.html.twig');
    }
}
