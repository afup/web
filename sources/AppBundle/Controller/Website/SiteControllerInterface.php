<?php


namespace AppBundle\Controller\Website;

use Afup\Site\Utils\Configuration;

interface SiteControllerInterface
{
    /**
     * @param array $blocks [
     *     'community' => string,
     *     'header' => string,
     *     'sidebar' => string,
     *     'social' => string,
     *     'footer' => string
     * ]
     * @return void
     */
    public function setDefaultBlocks(array $blocks);

    /**
     * @param Configuration $conf
     */
    public function setConfiguration(Configuration $conf);
}
