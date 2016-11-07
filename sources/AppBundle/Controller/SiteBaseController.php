<?php


namespace AppBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

abstract class SiteBaseController extends Controller implements SiteControllerInterface
{
    private $defaultBlocks = [];

    /**
     * @inheritDoc
     */
    public function setDefaultBlocks(array $blocks)
    {
        $this->defaultBlocks = $blocks;
    }

    /**
     * @inheritDoc
     */
    protected function render($view, array $parameters = array(), Response $response = null)
    {
        return parent::render($view, $parameters + $this->defaultBlocks, $response);
    }
}
