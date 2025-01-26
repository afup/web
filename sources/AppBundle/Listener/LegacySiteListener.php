<?php


namespace AppBundle\Listener;

use Afup\Site\Corporate\Page;
use AppBundle\Controller\Website\BlocksHandler;
use AppBundle\Controller\Website\SiteControllerInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\Security\Core\Security;

class LegacySiteListener
{
    /** @var Security */
    private $security;
    /** @var BlocksHandler */
    private $blocksHandler;

    public function __construct(Security $security, BlocksHandler $blocksHandler)
    {
        $this->security = $security;
        $this->blocksHandler = $blocksHandler;
    }

    public function onKernelController(FilterControllerEvent $event)
    {
        if ($event->isMasterRequest() === false) {
            return;
        }

        require_once __DIR__ . '/../../Afup/Bootstrap/Http.php';

        $page = new Page($GLOBALS['AFUP_DB']);
        $blocks = [
            'community' => $page->community(),
            'header' => $page->header($_SERVER['REQUEST_URI'], $this->security->getUser()),
            'sidebar' => $page->getRightColumn(),
            'social' => $page->social(),
            'footer' => $page->footer()
        ];
        $this->blocksHandler->setDefaultBlocks($blocks);
        // TODO: remove once SiteControllerInterface is removed in favor of BlocksHandler
        $controller = $event->getController();
        if (!is_array($controller) || [] === $controller || !$controller[0] instanceof SiteControllerInterface) {
            return;
        }
        /** @var SiteControllerInterface $controller */
        $controller = $controller[0];
        $controller->setDefaultBlocks($blocks);
        $controller->setConfiguration($GLOBALS['AFUP_CONF']);
    }
}
