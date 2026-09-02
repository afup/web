<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\TalkToSpeakerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TalkToSpeakerRepository::class)]
#[ORM\Table(name: 'afup_conferenciers_sessions')]
class TalkToSpeaker
{
    #[ORM\Id]
    #[ORM\Column(name: 'session_id')]
    public int $talkId;

    #[ORM\Id]
    #[ORM\Column(name: 'conferencier_id')]
    public int $speakerId;
}
