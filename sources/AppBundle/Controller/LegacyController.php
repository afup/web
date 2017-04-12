<?php


namespace AppBundle\Controller;


use Symfony\Component\HttpFoundation\Response;

class LegacyController
{
    public function voidAction()
    {
        return new Response();
    }
}
