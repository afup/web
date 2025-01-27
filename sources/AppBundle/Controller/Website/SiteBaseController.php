<?php


namespace AppBundle\Controller\Website;

use Afup\Site\Utils\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

abstract class SiteBaseController extends Controller implements SiteControllerInterface
{
    protected array $defaultBlocks = [];

    protected Configuration $legacyConfiguration;

    /**
     * @inheritDoc
     * @deprecated use BlocksHandler
     */
    public function setDefaultBlocks(array $blocks): void
    {
        $this->defaultBlocks = $blocks;
    }

    public function setConfiguration(Configuration $conf): void
    {
        $this->legacyConfiguration = $conf;
    }

    /**
     * @inheritDoc
     */
    protected function render($view, array $parameters = [], Response $response = null): Response
    {
        return parent::render($view, $parameters + $this->defaultBlocks, $response);
    }
}
