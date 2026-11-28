<?php

declare(strict_types=1);

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use AppBundle\Event\Entity\GithubUser;
use AppBundle\Event\Entity\Repository\SpeakerRepository;
use AppBundle\Event\Entity\Speaker;
use AppBundle\Event\Model\Talk;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class TalkVoter extends Voter
{
    public function __construct(private readonly SpeakerRepository $speakerRepository) {}

    /**
     * @inheritDoc
     */
    protected function supports($attribute, $subject): bool
    {
        if ($attribute !== 'edit' || !$subject instanceof Talk) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();
        if (!$user instanceof GithubUser) {
            return false;
        }

        $talkId = $subject instanceof Talk ? $subject->getId() : null;
        if ($talkId === null) {
            return false;
        }

        // All speakers associated to a talk can edit the talk
        return array_any(
            $this->speakerRepository->getSpeakersByTalk($talkId),
            static fn(Speaker $speaker): bool => $speaker->githubUserId === $user->getId(),
        );
    }
}
