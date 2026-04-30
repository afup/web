<?php

declare(strict_types=1);

namespace AppBundle\Event\Speaker;

enum MicrophoneType: string
{
    case Headset = 'headset';
    case Handheld = 'handheld';
}
