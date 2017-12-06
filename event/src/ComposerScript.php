<?php

namespace Afup\EventWordpress;

use Composer\Composer;
use Composer\Script\Event;

class ComposerScript
{
    protected static function getDirWithContentToSymlink($baseDir, $wordpressDir)
    {
        return [
            $baseDir . '/resources/web' => $wordpressDir . '/',
            $baseDir . '/resources/themes' => $wordpressDir  . '/wp-content/themes/',
            $baseDir . '/resources/languages' => $wordpressDir  . '/wp-content/languages/',
            $baseDir . '/resources/config' => $wordpressDir . '/',
        ];
    }

    protected static function getPathsToDelete($wordpressDir)
    {
        return [
            $wordpressDir . '/composer.json',
            $wordpressDir . '/wp-content/plugins/hello.php',
            $wordpressDir . '/wp-content/themes/content-aside.php',
            $wordpressDir . '/wp-content/themes/content-featured-post.php',
            $wordpressDir . '/wp-content/themes/search.php',
            $wordpressDir . '/wp-content/themes/twentyseventeen',
            $wordpressDir . '/wp-content/themes/twentysixteen',
        ];
    }

    public static function createSymlinks(Event $event)
    {
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        require_once $vendorDir . '/autoload.php';

        $filesystem = new \Symfony\Component\Filesystem\Filesystem();

        $wordpressDir = self::getWordpressDir($event->getComposer());

        $baseDir = $vendorDir . '/../';

        foreach (self::getDirWithContentToSymlink($baseDir, $wordpressDir) as $sourceDirectory => $wordpressTarget) {
            $finder = \Symfony\Component\Finder\Finder::create()
                ->ignoreDotFiles(false)
                ->depth(0)
                ->in($sourceDirectory)
            ;

            foreach ($finder as $file) {
                $origin = $file->getRealPath();
                $target = $wordpressTarget . $file->getBasename();
                $event->getIO()->write($origin . ' -> ' . $target);
                $filesystem->remove($target);
                $filesystem->symlink($origin, $target);
            }
        }

        foreach (self::getPathsToDelete($wordpressDir) as $pathToDelete) {
            $event->getIO()->write('remove ' . $pathToDelete);
            $filesystem->remove($pathToDelete);
        }
    }

    protected static function getWordpressDir(Composer $composer)
    {
        $package = $composer->getPackage();
        $extra = $package->getExtra();
        $wordpressDir = false;
        if (isset($extra['wordpress-install-dir'])) {
            $wordpressDir = is_array($extra['wordpress-install-dir']) ? array_shift($extra['wordpress-install-dir']) : $extra['wordpress-install-dir'];
        }

        if (false === $wordpressDir) {
            throw new \RuntimeException(('Wordpress dir not found'));
        }

        return $wordpressDir;
    }
}
