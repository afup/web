<?php

declare(strict_types=1);

use AppBundle\AppKernel;

$kernel = new AppKernel($_SERVER['APP_ENV'] ?? 'test', (bool) ($_SERVER['APP_DEBUG']) ?? true);
$kernel->boot();

return $kernel->getContainer()->get('doctrine')->getManager();
