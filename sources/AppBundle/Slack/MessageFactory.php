<?php


namespace AppBundle\Slack;


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
            ->setTitleLink('http://www.afup.org/pages/administration/index.php?page=forum_vote_github')
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
            ->setChannel('cfp')
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
            ->setTitleLink('http://www.afup.org/pages/administration/index.php?page=forum_sessions')
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
}
