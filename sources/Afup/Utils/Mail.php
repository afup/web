<?php

declare(strict_types=1);

namespace Afup\Site\Utils;

use AppBundle\Email\Mailer\Adapter\PhpMailerAdapter;
use AppBundle\Email\Mailer\Mailer;
use Psr\Log\NullLogger;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Mail
{
    public const PROJECT_DIR = __DIR__ . '/../../..';

    public static function createMailer(): Mailer
    {
        $configuration = new Configuration();

        return new Mailer(
            new NullLogger(),
            new Environment(new FilesystemLoader(self::PROJECT_DIR . '/templates/')),
            PhpMailerAdapter::createFromConfiguration($configuration),
            $configuration,
        );
    }
}
