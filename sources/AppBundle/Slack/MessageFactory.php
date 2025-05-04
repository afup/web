<?php

declare(strict_types=1);


namespace AppBundle\Slack;

use AppBundle\Association\Model\Repository\UserRepository;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\EventStatsRepository;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TalkToSpeakersRepository;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\Vote;
use AppBundle\GeneralMeeting\GeneralMeetingRepository;
use Assert\Assertion;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class MessageFactory
{
    /**
     * MessageFactory constructor.
     */
    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

    public function createMessageForVote(Vote $vote): Message
    {
        $attachment = new Attachment();
        $attachment
            ->setTitle('Nouveau vote sur le CFP')
            ->setTitleLink('https://afup.org/pages/administration/index.php?page=forum_vote_github')
            ->setFallback(sprintf(
                    'Nouveau vote sur la conférence "%s". Note: %s. Commentaire: %s',
                    $vote->getTalk()->getTitle(),
                    $vote->getVote(),
                    $vote->getComment()
                )
            )
            ->setColor('good')
            ->setMrkdwnIn(['text', 'fields'])
        ;

        $attachment
            ->addField(
                (new Field())->setShort(false)->setTitle('Talk')->setValue($vote->getTalk()->getTitle())
            )
            ->addField(
                (new Field())->setShort(false)->setTitle('Nouveau vote')->setValue(
                    str_repeat(':star:', $vote->getVote())
                )
            )
        ;
        if ($vote->getComment() !== null) {
            $attachment
                ->addField(
                    (new Field())->setShort(false)->setTitle('Commentaire')->setValue($vote->getComment())
                )
            ;
        }

        $message = new Message();
        $message
            ->setChannel('cfp-votes')
            ->addAttachment($attachment)
            ->setIconUrl('https://pbs.twimg.com/profile_images/600291061144145920/Lpf3TDQm_400x400.png')
            ->setUsername('CFP')
        ;

        return $message;
    }

    /**
     *
     * @return Message $message
     */
    public function createMessageForTalk(Talk $talk, Event $event): Message
    {
        $attachment = new Attachment();
        $attachment
            ->setTitle('Nouvelle proposition sur le CFP - ' . $event->getTitle())
            ->setTitleLink('https://afup.org/pages/administration/index.php?' . http_build_query(['page' => 'forum_sessions', 'id_forum' => $event->getId()]))
            ->setFallback(sprintf(
                    'Nouvelle proposition intitulée "%s". Type %s - Public %s',
                    $talk->getTitle(),
                    $this->translator->trans($talk->getTypeTranslationKey()),
                    $this->translator->trans($talk->getSkillTranslationKey())
                )
            )
            ->setColor('good')
            ->setMrkdwnIn(['text', 'fields'])
        ;

        $attachment
            ->addField(
                (new Field())->setShort(false)->setTitle('Talk')->setValue($talk->getTitle())
            )
            ->addField(
                (new Field())->setShort(false)->setTitle('Résumé')->setValue(substr($talk->getAbstract(), 0, 300))
            )
            ->addField(
                (new Field())->setShort(false)->setTitle('Accompagnement')->setValue($talk->getNeedsMentoring() ? "Oui": "Non")
            )
            ->addField(
                (new Field())->setShort(true)->setTitle('Type')->setValue($this->translator->trans($talk->getTypeTranslationKey()))
            )
            ->addField(
                (new Field())->setShort(true)->setTitle('Public')->setValue($this->translator->trans($talk->getSkillTranslationKey()))
            )
        ;

        $message = new Message();
        $message
            ->setChannel('cfp')
            ->addAttachment($attachment)
            ->setIconUrl('https://pbs.twimg.com/profile_images/600291061144145920/Lpf3TDQm_400x400.png')
            ->setUsername('CFP')
        ;

        return $message;
    }

    public function createMessageForMemberNotification(string $membersToCheckCount): Message
    {
        $attachment = new Attachment();
        $attachment
            ->setTitle('Vérification état slack membres')
            ->setTitleLink('https://afup.org/admin/slackmembers/check')
            ->setColor('#FF0000')
            ->addField(
                (new Field())->setShort(false)->setTitle('Membres à vérifier')->setValue($membersToCheckCount)
            )
        ;

        $message = new Message();
        $message
            ->setChannel('bureau')
            ->setIconUrl('https://pbs.twimg.com/profile_images/600291061144145920/Lpf3TDQm_400x400.png')
            ->setUsername('Slack membres')
            ->addAttachment($attachment)
        ;

        return $message;
    }

    public function createMessageForGeneralMeeting(GeneralMeetingRepository $generalMeetingRepository, UserRepository $userRepository, UrlGeneratorInterface $urlGenerator): Message
    {
        $latestDate = $generalMeetingRepository->getLatestDate();
        Assertion::notNull($latestDate);
        $nombrePersonnesAJourDeCotisation = count($userRepository->getActiveMembers(UserRepository::USER_TYPE_ALL));

        $message = new Message();
        $message
            ->setChannel('bureau')
            ->setUsername('Assemblée Générale')
            ->setIconUrl('https://pbs.twimg.com/profile_images/600291061144145920/Lpf3TDQm_400x400.png')
        ;

        $attachment = new Attachment();
        $attachment
            ->setTitleLink($urlGenerator->generate('admin_members_general_meeting', [], UrlGeneratorInterface::ABSOLUTE_URL))
            ->addField((new Field())->setShort(true)->setTitle('Membres à jour de cotisation')
                ->setValue($nombrePersonnesAJourDeCotisation))
            ->addField((new Field())->setShort(true)->setTitle('Présences et pouvoirs')
                ->setValue($generalMeetingRepository->countAttendeesAndPowers($latestDate)))
            ->addField((new Field())->setShort(true)->setTitle('Présences')
                ->setValue($generalMeetingRepository->countAttendees($latestDate)))
            ->addField((new Field())->setShort(true)->setTitle('Quorum')
                ->setValue($generalMeetingRepository->obtenirEcartQuorum($latestDate, $nombrePersonnesAJourDeCotisation)))
        ;
        $message->addAttachment($attachment);

        return $message;
    }

    public function createMessageForTicketStats(Event $event, EventStatsRepository $eventStatsRepository, TicketTypeRepository $ticketRepository, \DateTime $date = null): Message
    {
        $eventStats = $eventStatsRepository->getStats($event->getId());
        $message = new Message();
        $message
            ->setChannel($event->isAfupDay() ? 'afupday' : 'pole-forum')
            ->setUsername($event->getTitle() . ' - Inscriptions')
            ->setIconUrl('https://pbs.twimg.com/profile_images/600291061144145920/Lpf3TDQm_400x400.png')
        ;

        if ($date instanceof \DateTime) {
            $eventStatsFiltered = $eventStatsRepository->getStats($event->getId(), $date);

            $attachment = new Attachment();
            $attachment
                ->setTitle(sprintf('Liste des inscriptions depuis le %s : ', $date->format('d/m/Y H:i')))
            ;
            foreach ($eventStatsFiltered->ticketType->confirmed as $typeId => $value) {
                if (0 === $value) {
                    continue;
                }
                $attachment->addField((new Field())->setShort(true)->setTitle($ticketRepository->get($typeId)->getPrettyName())->setValue($value));
            }

            $message->addAttachment($attachment);
        }

        $attachment = new Attachment();
        $attachment
            ->setTitle('Total des inscriptions')
            ->setTitleLink('https://afup.org/pages/administration/index.php?page=forum_inscriptions')
        ;


        if ($event->lastsOneDay()) {
            $attachment->addField((new Field())->setShort(true)->setTitle('Journée unique')
                ->setValue($eventStats->firstDay->confirmed + $eventStats->firstDay->pending));
        } else {
            $attachment
                ->addField((new Field())->setShort(true)->setTitle('Premier jour')
                    ->setValue($eventStats->firstDay->confirmed + $eventStats->firstDay->pending))
                ->addField((new Field())->setShort(true)->setTitle('Deuxième jour')
                    ->setValue($eventStats->secondDay->confirmed + $eventStats->secondDay->pending))
            ;
        }

        $message->addAttachment($attachment);

        return $message;
    }


    public function createMessageForCfpStats(Event $event, TalkRepository $talkRepository, TalkToSpeakersRepository $talkToSpeakersRepository, \DateTime $currentDate, \DateTime $since = null): Message
    {
        $message = new Message();
        $message
            ->setChannel($event->isAfupDay() ? 'afupday' : 'pole-forum')
            ->setUsername('CFP')
            ->setIconUrl('https://pbs.twimg.com/profile_images/600291061144145920/Lpf3TDQm_400x400.png')
        ;

        if ($since instanceof \DateTime) {
            //Il n'y a pas les heures dans les dates de soumission en base
            $since = clone $since;
            $since->setTime(0, 0, 0);

            $fields = $this->prepareCfpStatsFields($talkRepository, $talkToSpeakersRepository, $event, $since);

            if ($fields !== []) {
                $attachment = new Attachment();
                $attachment
                    ->setTitle(sprintf('Réponses au CFP du %s depuis le %s : ', $event->getTitle(), $since->format('d/m/Y H:i')))
                ;

                foreach ($fields as $field) {
                    $attachment->addField($field);
                }

                $message->addAttachment($attachment);
            }
        }

        $attachment = new Attachment();
        $attachment
            ->setTitle(sprintf('Total des réponses au CFP du %s', $event->getTitle()))
            ->setTitleLink('https://afup.org/pages/administration/index.php?page=forum_sessions')
        ;

        foreach ($this->prepareCfpStatsFields($talkRepository, $talkToSpeakersRepository, $event) as $field) {
            $attachment->addField($field);
        }

        $message->addAttachment($attachment);

        $diff = $event->getDateEndCallForPapers()->diff($currentDate)->format("%a");

        $attachment = new Attachment();
        $attachment->setTitle(sprintf('Il reste %s jours avant la fin du CFP.', $diff));
        $message->addAttachment($attachment);

        return $message;
    }

    /**
     * @return Field[]
     */
    private function prepareCfpStatsFields(TalkRepository $talkRepository, TalkToSpeakersRepository $talkToSpeakersRepository, Event $event, \DateTime $since = null): array
    {
        $infos = [
            'Nombre de talks' => $talkRepository->getNumberOfTalksByEvent($event, $since)['talks'],
            'Nombre de speakers' => $talkToSpeakersRepository->getNumberOfSpeakers($event, $since),
        ];

        $fields = [];
        foreach ($infos as $title => $value) {
            $fields[] = (new Field())->setShort(true)->setTitle($title)->setValue($value);
        }

        return $fields;
    }
}
