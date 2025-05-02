<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

enum LegendAttachment: string
{
    case Top = 'top';
    case Bottom = 'bottom';
    case Left = 'left';
    case Right = 'right';
}
