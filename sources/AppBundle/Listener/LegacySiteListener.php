<?php


namespace AppBundle\Listener;


use Afup\Site\Corporate\Page;
use Afup\Site\Utils\Base_De_Donnees;
use AppBundle\Controller\SiteControllerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;

class LegacySiteListener
{
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

        require_once dirname(__FILE__) .'/../../Afup/Bootstrap/Http.php';

        /**
         * @var $bdd Base_De_Donnees
         */
        $page = new Page($GLOBALS['AFUP_DB']);

        $controller->setDefaultBlocks([
            'community' => $page->community(),
            'header' => $page->header(),
            'sidebar' => $page->getRightColumn(),
            'social' => $page->social(),
            'footer' => $page->footer()
        ]);

        $controller->setConfiguration($GLOBALS['AFUP_CONF']);
    }
}
