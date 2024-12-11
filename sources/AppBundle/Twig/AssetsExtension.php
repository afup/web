<?php


namespace AppBundle\Twig;

class AssetsExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    private $kernelRootDir;

    public function __construct($kernelRootDir)
    {
        $this->kernelRootDir = $kernelRootDir;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('asset_md5_start', function ($url) {
                $path = $this->kernelRootDir . '/../htdocs/' . $url;

                return substr(md5_file($path), 0, 8);
            }, ['is_safe' => ['html']])
        ];
    }

    public function getName()
    {
        return 'assets';
    }

    public function getGlobals()
    {
        return [];
    }
}
