<?php

declare(strict_types=1);

namespace AppBundle\Event\Enum;

enum TalkInvitationState: int
{
    case Pending = 0;
    case Accepted = 1;
}
