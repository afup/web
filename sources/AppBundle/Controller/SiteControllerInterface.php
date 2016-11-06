<?php


namespace AppBundle\Controller;


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
}
