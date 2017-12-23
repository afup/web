<?php


namespace AppBundle\Slack;

use Afup\Site\Association\Assemblee_Generale;
use Afup\Site\Forum\Inscriptions;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Repository\TalkRepository;
use AppBundle\Event\Model\Repository\TalkToSpeakersRepository;
use AppBundle\Event\Model\Repository\TicketTypeRepository;
use AppBundle\Event\Model\Talk;
use AppBundle\Event\Model\Vote;
use Symfony\Component\Translation\Translator;

class MessageFactory
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * MessageFactory constructor.
     * @param Translator $translator
     */
    public function __construct(Translator $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param Vote $vote
     * @return Message
     */
    public function createMessageForVote(Vote $vote)
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
     * @param Talk $talk
     * @return Message $message
     */
    public function createMessageForTalk(Talk $talk)
    {
        $attachment = new Attachment();
        $attachment
            ->setTitle('Nouvelle proposition sur le CFP')
            ->setTitleLink('https://afup.org/pages/administration/index.php?page=forum_sessions')
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

    public function createMessageForGeneralMeeting(Assemblee_Generale $assembleeGenerale)
    {
        $timestamp = $assembleeGenerale->obternirDerniereDate();

        $message = new Message();
        $message
            ->setChannel('bureau')
            ->setUsername('Assemblée Générale')
            ->setIconUrl('https://pbs.twimg.com/profile_images/600291061144145920/Lpf3TDQm_400x400.png')
        ;

        $attachment = new Attachment();
        $attachment
            ->setTitleLink('https://afup.org/pages/administration/index.php?page=assemblee_generale')
            ->addField(
                (new Field())->setShort(true)->setTitle('Membres à jour de cotisation')->setValue($assembleeGenerale->obtenirNombrePersonnesAJourDeCotisation($timestamp))
            )
            ->addField(
                (new Field())->setShort(true)->setTitle('Présences et pouvoirs')->setValue($assembleeGenerale->obtenirNombrePresencesEtPouvoirs($timestamp))
            )
            ->addField(
                (new Field())->setShort(true)->setTitle('Présences')->setValue($assembleeGenerale->obtenirNombrePresences($timestamp))
            )
            ->addField(
                (new Field())->setShort(true)->setTitle('Quorum')->setValue($assembleeGenerale->obtenirEcartQuorum($timestamp))
            )
        ;
        $message->addAttachment($attachment);

        return $message;
    }

    /**
     * @param Event $event
     * @param Inscriptions $inscriptions
     * @param TicketTypeRepository $ticketRepository
     * @param \DateTime $date
     *
     * @return Message
     */
    public function createMessageForTicketStats(Event $event, Inscriptions $inscriptions, TicketTypeRepository $ticketRepository, \DateTime $date = null)
    {
        $inscriptionsData = $inscriptions->obtenirStatistiques($event->getId());
        $message = new Message();
        $message
            ->setChannel('bureau')
            ->setUsername('Inscriptions')
            ->setIconUrl('https://pbs.twimg.com/profile_images/600291061144145920/Lpf3TDQm_400x400.png')
        ;

        if (null !== $date) {
            $inscriptionsDataFiltered = $inscriptions->obtenirStatistiques($event->getId(), $date);

            $attachment = new Attachment();
            $attachment
                ->setTitle(sprintf('Liste des inscriptions depuis le %s : ', $date->format('d/m/Y H:i')))
            ;
            foreach ($inscriptionsDataFiltered['types_inscriptions']['inscrits'] as $typeId => $value) {
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
            ->addField(
                (new Field())->setShort(true)->setTitle('Premier jour')->setValue($inscriptionsData['premier_jour']['inscrits'])
            )
            ->addField(
                (new Field())->setShort(true)->setTitle('Deuxième jour')->setValue($inscriptionsData['second_jour']['inscrits'])
            )
        ;
        $message->addAttachment($attachment);

        return $message;
    }


    public function createMessageForCfpStats(Event $event, TalkRepository $talkRepository, TalkToSpeakersRepository $talkToSpeakersRepository, \DateTime $since, \DateTime $currentDate)
    {
        //Il n'y a pas les heures dans les dates de soumission en base
        $since = clone $since;
        $since->setTime(0, 0, 0);

        $message = new Message();
        $message
            ->setChannel('phptour2018')
            ->setUsername('CFP')
            ->setIconUrl('https://pbs.twimg.com/profile_images/600291061144145920/Lpf3TDQm_400x400.png')
        ;

        $fields = $this->prepareCfpStatsFields($talkRepository, $talkToSpeakersRepository, $event, $since);

        if (count($fields)) {
            $attachment = new Attachment();
            $attachment
                ->setTitle(sprintf('Réponses au CFP du %s depuis le %s : ', $event->getTitle(), $since->format('d/m/Y H:i')))
            ;

            foreach ($fields as $field) {
                $attachment->addField($field);
            }

            $message->addAttachment($attachment);
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

    private function prepareCfpStatsFields(TalkRepository $talkRepository, TalkToSpeakersRepository $talkToSpeakersRepository, Event $event, \DateTime $since = null)
    {
        $infos = [
            'Nombre de talks' => $talkRepository->getNumberOfTalksByEvent($event, $since)['talks'],
            'Nombre de speakers' => $talkToSpeakersRepository->getNumberOfSpeakers($event, $since),
        ];

        if (0 === array_sum($infos)) {
            return [];
        }

        $fields = [];
        foreach ($infos as $title => $value) {
            $fields[] = (new Field())->setShort(true)->setTitle($title)->setValue($value);
        }

        return $fields;
    }
}
