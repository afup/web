<?php

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Cocur\Slugify\Slugify;
use Symfony\Component\Validator\Constraints as Assert;

class Talk implements NotifyPropertyInterface
{
    use NotifyProperty;

    const TYPE_FULL_LONG = 1;
    const TYPE_FULL_SHORT = 3;
    const TYPE_WORKSHOP = 2;
    const TYPE_KEYNOTE = 4;
    const TYPE_LIGHTNING_TALK = 5;
    const TYPE_CLINIC = 6;
    const TYPE_PHP_PROJECT = 9;
    const TYPE_SPEAKER_INTRODUCTIONS = 7;

    const SKILL_JUNIOR = 1;
    const SKILL_MEDIOR = 2;
    const SKILL_SENIOR = 3;
    const SKILL_NA = 0;

    const LANGUAGE_CODE_FR = 'fr';
    const LANGUAGE_CODE_EN = 'en';

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $forumId;

    /**
     * @var \DateTime
     */
    private $submittedOn;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $title;

    /**
     * @var string
     * @Assert\NotBlank()
     */
    private $abstract;

    /**
     * @var string
     */
    private $staffNotes;

    /**
     * @var bool
     */
    private $scheduled = false;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {1, 2, 3}, message = "Choose a valid type")
     */
    private $type = self::TYPE_FULL_LONG;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {0, 1, 2, 3}, message = "Choose a valid skill requirement")
     */
    private $skill = self::SKILL_NA;

    /**
     * @var bool
     */
    private $withWorkshop = false;

    /**
     * @var string
     */
    private $workshopAbstract;

    /**
     * @var bool
     */
    private $needsMentoring = false;

    /**
     * @var string|null
     */
    private $youTubeId;

    /**
     * @var bool
     */
    private $videoHasFrSubtitles = false;

    /**
     * @var bool
     */
    private $videoHasEnSubtitles = false;

    /**
     * @var string|null
     */
    private $slidesUrl;

    /**
     * @var string|null
     */
    private $blogPostUrl;

    /**
     * @var string|null
     */
    private $interviewUrl;

    /**
     * @var string|null
     */
    private $joindinId;

    /**
     * @var string|null
     */
    private $openfeedbackPath;

    /**
     * @var string|null
     */
    private $languageCode;

    /**
     * @var string|null
     */
    private $tweets;

    /**
     * @var string|null
     */
    private $transcript;

    /**
     * @var string|null
     */
    private $verbatim;

    /**
     * @var bool
     */
    private $useMarkdown = true;

    /**
     * @var bool
     * @Assert\NotNull()
     */
    private $hasAllowedToSharingWithLocalOffices = false;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Talk
     */
    public function setId($id)
    {
        $id = (int) $id;
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getForumId()
    {
        return $this->forumId;
    }

    /**
     * @param int $forumId
     * @return Talk
     */
    public function setForumId($forumId)
    {
        $forumId = (int) $forumId;
        $this->propertyChanged('forumId', $this->forumId, $forumId);
        $this->forumId = $forumId;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSubmittedOn()
    {
        return $this->submittedOn;
    }

    /**
     * @param \DateTime $submittedOn
     * @return Talk
     */
    public function setSubmittedOn(\DateTime $submittedOn)
    {
        $this->propertyChanged('submittedOn', $this->submittedOn, $submittedOn);
        $this->submittedOn = $submittedOn;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Talk
     */
    public function setTitle($title)
    {
        $this->propertyChanged('title', $this->title, $title);
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * @param string $abstract
     * @return Talk
     */
    public function setAbstract($abstract)
    {
        $this->propertyChanged('abstract', $this->abstract, $abstract);
        $this->abstract = $abstract;
        return $this;
    }

    /**
     * @return string
     */
    public function getStaffNotes()
    {
        return $this->staffNotes;
    }

    /**
     * @param string $staffNotes
     *
     * @return $this
     */
    public function setStaffNotes($staffNotes)
    {
        $this->propertyChanged('staffNotes', $this->staffNotes, $staffNotes);
        $this->staffNotes = $staffNotes;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return preg_replace("/NIVEAU : .*\n/", "", $this->getAbstract());
    }

    /**
     * @return boolean
     */
    public function getScheduled()
    {
        return $this->scheduled;
    }

    /**
     * @param boolean $scheduled
     * @return Talk
     */
    public function setScheduled($scheduled)
    {
        $scheduled = (bool) $scheduled;

        $this->propertyChanged('scheduled', $this->scheduled, $scheduled);
        $this->scheduled = $scheduled;
        return $this;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     * @return Talk
     */
    public function setType($type)
    {
        $this->propertyChanged('type', $this->type, $type);
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getSkill()
    {
        return $this->skill;
    }

    /**
     * @param int $skill
     * @return Talk
     */
    public function setSkill($skill)
    {
        $this->propertyChanged('skill', $this->skill, $skill);
        $this->skill = $skill;
        return $this;
    }

    public function getSkillTranslationKey()
    {
        return 'skill.' . $this->skill;
    }

    public function getTypeTranslationKey()
    {
        return 'type.' . $this->type;
    }

    /**
     * @return bool
     */
    public function getWithWorkshop()
    {
        return $this->withWorkshop;
    }

    /**
     * @param bool $withWorkshop
     *
     * @return $this
     */
    public function setWithWorkshop($withWorkshop)
    {
        $this->propertyChanged('withWorkshop', $this->withWorkshop, $withWorkshop);
        $this->withWorkshop = $withWorkshop;

        return $this;
    }

    /**
     * @return string
     */
    public function getWorkshopAbstract()
    {
        return $this->workshopAbstract;
    }

    /**
     * @param string $workshopAbstract
     * @return Talk
     */
    public function setWorkshopAbstract($workshopAbstract)
    {
        $this->propertyChanged('workshopAbstract', $this->workshopAbstract, $workshopAbstract);
        $this->workshopAbstract = $workshopAbstract;
        return $this;
    }

    /**
     * @return bool
     */
    public function getNeedsMentoring()
    {
        return $this->needsMentoring;
    }

    /**
     * @param bool $needsMentoring
     *
     * @return $this
     */
    public function setNeedsMentoring($needsMentoring)
    {
        $this->propertyChanged('needsMentoring', $this->needsMentoring, $needsMentoring);
        $this->needsMentoring = $needsMentoring;

        return $this;
    }

    /**
     * @return int
     */
    public function getYoutubeId()
    {
        if (0 === strlen($this->youTubeId)) {
            return null;
        }

        return $this->youTubeId;
    }

    /**
     * @param int $youtubeId
     *
     * @return Talk
     */
    public function setYoutubeId($youtubeId)
    {
        $this->propertyChanged('youtubeId', $this->youTubeId, $youtubeId);
        $this->youTubeId = $youtubeId;

        return $this;
    }

    /**
     * @return null|string
     */
    public function hasYoutubeId()
    {
        return null !== $this->getYoutubeId();
    }

    /**
     * @return null|string
     */
    public function getYoutubeUrl()
    {
        if (!$this->hasYoutubeId()) {
            return null;
        }

        return 'https://www.youtube.com/watch?v=' . $this->getYoutubeId();
    }

    /**
     * @return int
     */
    public function getSlidesUrl()
    {
        if (0 === strlen($this->slidesUrl)) {
            return null;
        }

        return $this->slidesUrl;
    }

    /**
     * @param int $slidesUrl
     *
     * @return Talk
     */
    public function setSlidesUrl($slidesUrl)
    {
        $this->propertyChanged('slidesUrl', $this->slidesUrl, $slidesUrl);
        $this->slidesUrl = $slidesUrl;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSlidesUrl()
    {
        return null !== $this->getSlidesUrl();
    }

    /**
     * @return int
     */
    public function getJoindinId()
    {
        if (0 === $this->joindinId) {
            return null;
        }

        return $this->joindinId;
    }

    /**
     * @param int $joindInId
     *
     * @return Talk
     */
    public function setJoindinId($joindInId)
    {
        $this->propertyChanged('joindinId', $this->joindinId, $joindInId);
        $this->joindinId = $joindInId;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasJoindinId()
    {
        return null !== $this->getJoindinId();
    }

    /**
     * @return null|string
     */
    public function getJoindinUrl()
    {
        if (!$this->hasJoindinId()) {
            return null;
        }

        return '/talks/' . $this->getUrlKey() . '/joindin';
    }

    /**
     * @return string|null
     */
    public function getOpenfeedbackPath()
    {
        return $this->openfeedbackPath;
    }

    /**
     * @param string|null $openfeedbackPath
     */
    public function setOpenfeedbackPath($openfeedbackPath)
    {
        $this->openfeedbackPath = $openfeedbackPath;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasOpenfeedbackPath()
    {
        return null !== $this->getOpenfeedbackPath();
    }

    /**
     * @return null|string
     */
    public function getOpenfeedbackUrl()
    {
        if (!$this->hasOpenfeedbackPath()) {
            return null;
        }

        return 'https://openfeedback.io/' . $this->getOpenfeedbackPath();
    }

    /**
     * @return int
     */
    public function getBlogPostUrl()
    {
        if (0 === strlen($this->blogPostUrl)) {
            return null;
        }

        return $this->blogPostUrl;
    }

    /**
     * @param int $blogPostUrl
     *
     * @return Talk
     */
    public function setBlogPostUrl($blogPostUrl)
    {
        $this->propertyChanged('blogPostUrl', $this->blogPostUrl, $blogPostUrl);
        $this->blogPostUrl = $blogPostUrl;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasBlogPostUrl()
    {
        return null !== $this->getBlogPostUrl();
    }

    /**
     * @return int
     */
    public function getInterviewUrl()
    {
        if (0 === strlen($this->interviewUrl)) {
            return null;
        }

        return $this->interviewUrl;
    }

    /**
     * @param int $interviewUrl
     *
     * @return Talk
     */
    public function setInterviewUrl($interviewUrl)
    {
        $this->propertyChanged('interviewUrl', $this->interviewUrl, $interviewUrl);
        $this->interviewUrl = $interviewUrl;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasInterviewUrl()
    {
        return null !== $this->getInterviewUrl();
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        $slugify = new Slugify();
        return $slugify->slugify($this->getTitle());
    }

    /**
     * @return string
     */
    public function getUrlKey()
    {
        return $this->getId() . '-' . $this->getSlug();
    }

    /**
     * @return array
     */
    public static function getTypeLabelsByKey()
    {
        return [
            self::TYPE_FULL_LONG => 'Conférence (40 minutes)',
            self::TYPE_WORKSHOP => 'Atelier',
            self::TYPE_FULL_SHORT => 'Conférence (20 minutes)',
            self::TYPE_KEYNOTE => 'Keynote',
            self::TYPE_LIGHTNING_TALK => 'Lightning Talk',
            self::TYPE_CLINIC => 'Clinique',
            self::TYPE_PHP_PROJECT => 'Projet PHP',
            self::TYPE_SPEAKER_INTRODUCTIONS => 'Introductions des speakers',
        ];
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getTypeLabel()
    {
        $type = $this->getType();
        $mapping = self::getTypeLabelsByKey();

        if (!isset($mapping[$type])) {
            throw new \Exception(sprintf('Type inconnue: %s', $type));
        }

        return $mapping[$type];
    }

    /**
     * @return bool
     */
    public function isDisplayedOnHistory()
    {
        $type = $this->getType();

        return $type != self::TYPE_CLINIC && $type != self::TYPE_SPEAKER_INTRODUCTIONS && true === $this->getScheduled();
    }

    /**
     * @return array
     */
    public static function getLanguageLabelsByKey()
    {
        return [
            self::LANGUAGE_CODE_FR => 'Français',
            self::LANGUAGE_CODE_EN => 'Anglais',
        ];
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    public function getLanguageLabel()
    {
        $languageCde = $this->getLanguageCode();
        $mapping = self::getLanguageLabelsByKey();

        if (!isset($mapping[$languageCde])) {
            throw new \Exception(sprintf('Code de langue inconnu : %s', $languageCde));
        }

        return $mapping[$languageCde];
    }

    /**
     * @return int
     */
    public function getLanguageCode()
    {
        return $this->languageCode;
    }

    /**
     * @param int $languageCode
     *
     * @return Talk
     */
    public function setLanguageCode($languageCode)
    {
        $this->propertyChanged('languageCode', $this->languageCode, $languageCode);
        $this->languageCode = $languageCode;

        return $this;
    }

    /**
     * @return bool
     */
    public function getUseMarkdown()
    {
        return $this->useMarkdown;
    }

    /**
     * @param bool $useMarkdown
     * @return Talk
     */
    public function setUseMarkdown($useMarkdown)
    {
        $useMarkdown = (bool) $useMarkdown;
        $this->propertyChanged('useMarkdown', $this->useMarkdown, $useMarkdown);
        $this->useMarkdown = $useMarkdown;
        return $this;
    }

    /**
     * @return bool
     */
    public function getVideoHasEnSubtitles()
    {
        return $this->videoHasEnSubtitles;
    }

    /**
     * @param bool $videoHasEnSubtitles
     *
     * @return $this
     */
    public function setVideoHasEnSubtitles($videoHasEnSubtitles)
    {
        $videoHasEnSubtitles = (bool) $videoHasEnSubtitles;
        $this->propertyChanged('useMarkdown', $this->videoHasEnSubtitles, $videoHasEnSubtitles);
        $this->videoHasEnSubtitles = $videoHasEnSubtitles;

        return $this;
    }

    /**
     * @return bool
     */
    public function getVideoHasFrSubtitles()
    {
        return $this->videoHasFrSubtitles;
    }

    /**
     * @param bool $videoHasFrSubtitles
     *
     * @return $this
     */
    public function setVideoHasFrSubtitles($videoHasFrSubtitles)
    {
        $videoHasFrSubtitles = (bool) $videoHasFrSubtitles;
        $this->propertyChanged('useMarkdown', $this->videoHasFrSubtitles, $videoHasFrSubtitles);
        $this->videoHasFrSubtitles = $videoHasFrSubtitles;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTweets()
    {
        return $this->tweets;
    }

    /**
     * @param string|null $tweets
     *
     * @return $this
     */
    public function setTweets($tweets)
    {
        $this->tweets = $tweets;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getTranscript()
    {
        return $this->transcript;
    }

    /**
     * @param string|null $transcript
     *
     * @return $this
     */
    public function setTranscript($transcript)
    {
        $this->transcript = $transcript;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getVerbatim()
    {
        return $this->verbatim;
    }

    /**
     * @param string|null $verbatim
     *
     * @return $this
     */
    public function setVerbatim($verbatim)
    {
        $this->verbatim = $verbatim;

        return $this;
    }


    /**
     * @return array
     */
    public function getTweetsHasArray()
    {
        $explodedTweets = explode(PHP_EOL, $this->getTweets());
        $returnedTweets = [];
        foreach ($explodedTweets as $explodedTweet) {
            $explodedTweet = trim($explodedTweet);
            if (0 === strlen($explodedTweet)) {
                continue;
            }

            $returnedTweets[] = $explodedTweet;
        }

        return $returnedTweets;
    }

    /**
     * @return bool|null
     */
    public function getHasAllowedToSharingWithLocalOffices()
    {
        return $this->hasAllowedToSharingWithLocalOffices;
    }

    public function setHasAllowedToSharingWithLocalOffices(bool $hasAllowedToSharingWithLocalOffices): self
    {
        $this->propertyChanged('hasAllowedToSharingWithLocalOffices', $this->hasAllowedToSharingWithLocalOffices, $hasAllowedToSharingWithLocalOffices);
        $this->hasAllowedToSharingWithLocalOffices = $hasAllowedToSharingWithLocalOffices;

        return $this;
    }
}
