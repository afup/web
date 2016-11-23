<?php


namespace AppBundle\Controller;


use Afup\Site\Utils\Configuration;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

abstract class SiteBaseController extends Controller implements SiteControllerInterface
{
    protected $defaultBlocks = [];

    /**
     * @var Configuration
     */
    protected $legacyConfiguration;

    /**
     * @inheritDoc
     */
    public function setDefaultBlocks(array $blocks)
    {
        $this->defaultBlocks = $blocks;
    }

    /**
     * @param Configuration $conf
     */
    public function setConfiguration(Configuration $conf)
    {
        $this->legacyConfiguration = $conf;
    }

    /**
     * @inheritDoc
     */
    protected function render($view, array $parameters = array(), Response $response = null)
    {
        return parent::render($view, $parameters + $this->defaultBlocks, $response);
    }
}
