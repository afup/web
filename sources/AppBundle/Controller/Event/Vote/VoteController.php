<?php

declare(strict_types=1);

namespace AppBundle\Controller\Event\Vote;

use AppBundle\Event\Form\VoteType;
use AppBundle\Event\Model\Vote;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

abstract class VoteController extends AbstractController
{
    protected function createVoteForm(string $eventSlug, int $talkId, Vote $vote): FormInterface
    {
        $vote->setSessionId($talkId);

        return $this
            ->createFormBuilder()->create(
                'vote' . $talkId,
                VoteType::class,
                ['data' => $vote],
            )->setAction(
                $this->generateUrl('vote_new', ['talkId' => $talkId, 'eventSlug' => $eventSlug]),
            )
            ->setMethod(Request::METHOD_POST)
            ->getForm();
    }
}
