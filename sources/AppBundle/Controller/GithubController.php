<?php


namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class GithubController extends Controller
{
    /**
     * Link to this controller to start the "connect" process
     *
     */
    public function connectAction()
    {
        return $this->get('oauth2.registry')
            ->getClient('github_main')
            ->redirect();
    }

    /**
     * Github redirects to back here afterwards
     *
     * @return Response
     */
    public function connectCheckAction()
    {
        return new RedirectResponse($this->generateUrl('connection_github'));
    }
}
