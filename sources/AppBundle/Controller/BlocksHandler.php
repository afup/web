<?php

namespace AppBundle\Controller;

class BlocksHandler
{
    /** @var array<string, string> */
    protected $defaultBlocks = [];

    /**
     * @param array{
     *     community: string,
     *     header: string,
     *     sidebar: string,
     *     social: string,
     *     footer: string
     * } $blocks
     *
     * @return void
     */
    public function setDefaultBlocks(array $blocks)
    {
        $this->defaultBlocks = $blocks;
    }

    /**
     * @return array<string, string>
     */
    public function getDefaultBlocks()
    {
        return $this->defaultBlocks;
    }
}
