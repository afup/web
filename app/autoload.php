<?php

use Composer\Autoload\ClassLoader;
use Doctrine\Common\Annotations\AnnotationRegistry;

/** @var ClassLoader $loader */
$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader([$loader, 'loadClass']);

require_once(dirname(__FILE__) . '/../sources/Afup/Bootstrap/_Common.php');
require_once(dirname(__FILE__) . '/../sources/Afup/Bootstrap/commonStart.php');

return $loader;
