<?php

declare(strict_types=1);

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

    private ?int $id = null;

    /**
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private ?int $forumId = null;

    private ?\DateTime $submittedOn = null;

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

    private bool $scheduled = false;

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

    private bool $videoHasFrSubtitles = false;

    private bool $videoHasEnSubtitles = false;

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

    private bool $useMarkdown = true;

    /**
     * @Assert\NotNull()
     */
    private bool $hasAllowedToSharingWithLocalOffices = false;

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id): self
    {
        $id = (int) $id;
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getForumId(): ?int
    {
        return $this->forumId;
    }

    /**
     * @param int $forumId
     */
    public function setForumId($forumId): self
    {
        $forumId = (int) $forumId;
        $this->propertyChanged('forumId', $this->forumId, $forumId);
        $this->forumId = $forumId;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getSubmittedOn(): ?\DateTime
    {
        return $this->submittedOn;
    }

    public function setSubmittedOn(\DateTime $submittedOn): self
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
     */
    public function setTitle($title): self
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
     */
    public function setAbstract($abstract): self
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
    public function setStaffNotes($staffNotes): self
    {
        $this->propertyChanged('staffNotes', $this->staffNotes, $staffNotes);
        $this->staffNotes = $staffNotes;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return preg_replace("/NIVEAU : .*\n/", "", $this->getAbstract());
    }

    public function getScheduled(): bool
    {
        return $this->scheduled;
    }

    /**
     * @param boolean $scheduled
     */
    public function setScheduled($scheduled): self
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
     */
    public function setType($type): self
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
     */
    public function setSkill($skill): self
    {
        $this->propertyChanged('skill', $this->skill, $skill);
        $this->skill = $skill;
        return $this;
    }

    public function getSkillTranslationKey(): string
    {
        return 'skill.' . $this->skill;
    }

    public function getTypeTranslationKey(): string
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
    public function setWithWorkshop($withWorkshop): self
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
     */
    public function setWorkshopAbstract($workshopAbstract): self
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
    public function setNeedsMentoring($needsMentoring): self
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
        if (empty($this->youTubeId)) {
            return null;
        }

        return $this->youTubeId;
    }

    /**
     * @param int $youtubeId
     */
    public function setYoutubeId($youtubeId): self
    {
        $this->propertyChanged('youtubeId', $this->youTubeId, $youtubeId);
        $this->youTubeId = $youtubeId;

        return $this;
    }

    public function hasYoutubeId(): bool
    {
        return null !== $this->getYoutubeId();
    }

    public function getYoutubeUrl(): ?string
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
        if (empty($this->slidesUrl)) {
            return null;
        }

        return $this->slidesUrl;
    }

    /**
     * @param int $slidesUrl
     */
    public function setSlidesUrl($slidesUrl): self
    {
        $this->propertyChanged('slidesUrl', $this->slidesUrl, $slidesUrl);
        $this->slidesUrl = $slidesUrl;

        return $this;
    }

    public function hasSlidesUrl(): bool
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
     */
    public function setJoindinId($joindInId): self
    {
        $this->propertyChanged('joindinId', $this->joindinId, $joindInId);
        $this->joindinId = $joindInId;

        return $this;
    }

    public function hasJoindinId(): bool
    {
        return null !== $this->getJoindinId();
    }

    public function getJoindinUrl(): ?string
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
    public function setOpenfeedbackPath($openfeedbackPath): self
    {
        $this->openfeedbackPath = $openfeedbackPath;

        return $this;
    }

    public function hasOpenfeedbackPath(): bool
    {
        return null !== $this->getOpenfeedbackPath();
    }

    public function getOpenfeedbackUrl(): ?string
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
        if (empty($this->blogPostUrl)) {
            return null;
        }

        return $this->blogPostUrl;
    }

    /**
     * @param int $blogPostUrl
     */
    public function setBlogPostUrl($blogPostUrl): self
    {
        $this->propertyChanged('blogPostUrl', $this->blogPostUrl, $blogPostUrl);
        $this->blogPostUrl = $blogPostUrl;

        return $this;
    }

    public function hasBlogPostUrl(): bool
    {
        return null !== $this->getBlogPostUrl();
    }

    /**
     * @return int
     */
    public function getInterviewUrl()
    {
        if (empty($this->interviewUrl)) {
            return null;
        }

        return $this->interviewUrl;
    }

    /**
     * @param int $interviewUrl
     */
    public function setInterviewUrl($interviewUrl): self
    {
        $this->propertyChanged('interviewUrl', $this->interviewUrl, $interviewUrl);
        $this->interviewUrl = $interviewUrl;

        return $this;
    }

    public function hasInterviewUrl(): bool
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

    public function getUrlKey(): string
    {
        return $this->getId() . '-' . $this->getSlug();
    }

    public static function getTypeLabelsByKey(): array
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

    public function isDisplayedOnHistory(): bool
    {
        $type = $this->getType();

        return $type != self::TYPE_CLINIC && $type != self::TYPE_SPEAKER_INTRODUCTIONS && $this->getScheduled();
    }

    public static function getLanguageLabelsByKey(): array
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
     */
    public function setLanguageCode($languageCode): self
    {
        $this->propertyChanged('languageCode', $this->languageCode, $languageCode);
        $this->languageCode = $languageCode;

        return $this;
    }

    public function getUseMarkdown(): bool
    {
        return $this->useMarkdown;
    }

    /**
     * @param bool $useMarkdown
     */
    public function setUseMarkdown($useMarkdown): self
    {
        $useMarkdown = (bool) $useMarkdown;
        $this->propertyChanged('useMarkdown', $this->useMarkdown, $useMarkdown);
        $this->useMarkdown = $useMarkdown;
        return $this;
    }

    public function getVideoHasEnSubtitles(): bool
    {
        return $this->videoHasEnSubtitles;
    }

    /**
     * @param bool $videoHasEnSubtitles
     *
     * @return $this
     */
    public function setVideoHasEnSubtitles($videoHasEnSubtitles): self
    {
        $videoHasEnSubtitles = (bool) $videoHasEnSubtitles;
        $this->propertyChanged('useMarkdown', $this->videoHasEnSubtitles, $videoHasEnSubtitles);
        $this->videoHasEnSubtitles = $videoHasEnSubtitles;

        return $this;
    }

    public function getVideoHasFrSubtitles(): bool
    {
        return $this->videoHasFrSubtitles;
    }

    /**
     * @param bool $videoHasFrSubtitles
     *
     * @return $this
     */
    public function setVideoHasFrSubtitles($videoHasFrSubtitles): self
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
    public function setTweets($tweets): self
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
    public function setTranscript($transcript): self
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
    public function setVerbatim($verbatim): self
    {
        $this->verbatim = $verbatim;

        return $this;
    }


    public function getTweetsHasArray(): array
    {
        $explodedTweets = explode(PHP_EOL, $this->getTweets());
        $returnedTweets = [];
        foreach ($explodedTweets as $explodedTweet) {
            if ($explodedTweet === '' || $explodedTweet === '0') {
                continue;
            }

            $returnedTweets[] = trim($explodedTweet);
        }

        return $returnedTweets;
    }

    public function getHasAllowedToSharingWithLocalOffices(): bool
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
