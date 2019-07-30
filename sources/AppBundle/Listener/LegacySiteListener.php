<?php


namespace AppBundle\Listener;

use Afup\Site\Corporate\Page;
use Afup\Site\Utils\Base_De_Donnees;
use AppBundle\Controller\SiteControllerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class LegacySiteListener
{
    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    public function __construct(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if ($event->isMasterRequest() === false) {
            return ;
        }

        /**
         * @var $controller SiteControllerInterface
         */
        $controller = $event->getController()[0];

        if (! $controller instanceof SiteControllerInterface) {
            return ;
        }

        require_once dirname(__FILE__) . '/../../Afup/Bootstrap/Http.php';

        /**
         * @var $bdd Base_De_Donnees
         */
        $page = new Page($GLOBALS['AFUP_DB']);

        $controller->setDefaultBlocks([
            'community' => $page->community(),
            'header' => $page->header($_SERVER['REQUEST_URI'], $this->getUser()),
            'sidebar' => $page->getRightColumn(),
            'social' => $page->social(),
            'footer' => $page->footer()
        ]);

        $controller->setConfiguration($GLOBALS['AFUP_CONF']);
    }

    protected function getUser()
    {
        if (null === $token = $this->tokenStorage->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            return null;
        }

        return $user;
    }
}
