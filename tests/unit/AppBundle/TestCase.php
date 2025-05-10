<?php

declare(strict_types=1);

namespace AppBundle\Tests;

use Faker\Factory;
use Faker\Generator;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    private static ?Generator $faker = null;

    protected static function faker(): Generator
    {
        return self::$faker ??= Factory::create();
    }
}
