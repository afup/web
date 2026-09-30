<?php

declare(strict_types=1);

namespace AppBundle\Association\Enum;

enum InvitationEtat: int
{
    case EnAttente = 0;
    case Acceptee = 1;
    case Annulee = 2;
}
