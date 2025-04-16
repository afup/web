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
     * @Assert\NotBlank()
     */
    private string $title = '';

    /**
     * @Assert\NotBlank()
     */
    private string $abstract = '';

    private ?string $staffNotes = null;

    private bool $scheduled = false;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {1, 2, 3}, message = "Choose a valid type")
     */
    private int $type = self::TYPE_FULL_LONG;

    /**
     * @Assert\NotBlank()
     * @Assert\Choice(choices = {0, 1, 2, 3}, message = "Choose a valid skill requirement")
     */
    private int $skill = self::SKILL_NA;

    private bool $withWorkshop = false;

    private ?string $workshopAbstract = null;

    private bool $needsMentoring = false;

    private ?string $youtubeId = null;

    private bool $videoHasFrSubtitles = false;

    private bool $videoHasEnSubtitles = false;

    private ?string $slidesUrl = null;

    private ?string $blogPostUrl = null;

    private ?string $interviewUrl = null;

    private ?int $joindinId = null;

    private ?string $openfeedbackPath = null;

    private ?string $languageCode = null;

    private ?string $tweets = null;

    private ?string $transcript = null;

    private ?string $verbatim = null;

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

    public function setId(int $id): self
    {
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

    public function setForumId(?int $forumId): self
    {
        $this->propertyChanged('forumId', $this->forumId, $forumId);
        $this->forumId = $forumId;
        return $this;
    }

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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->propertyChanged('title', $this->title, $title);
        $this->title = $title;
        return $this;
    }

    public function getAbstract(): string
    {
        return $this->abstract;
    }

    public function setAbstract(string $abstract): self
    {
        $this->propertyChanged('abstract', $this->abstract, $abstract);
        $this->abstract = $abstract;
        return $this;
    }

    public function getStaffNotes(): ?string
    {
        return $this->staffNotes;
    }

    public function setStaffNotes(?string $staffNotes): self
    {
        $this->propertyChanged('staffNotes', $this->staffNotes, $staffNotes);
        $this->staffNotes = $staffNotes;

        return $this;
    }

    public function getDescription(): ?string
    {
        return preg_replace("/NIVEAU : .*\n/", "", $this->getAbstract());
    }

    public function getScheduled(): bool
    {
        return $this->scheduled;
    }

    public function setScheduled(?bool $scheduled): self
    {
        if (null === $scheduled) {
            $scheduled = false;
        }

        $this->propertyChanged('scheduled', $this->scheduled, $scheduled);
        $this->scheduled = $scheduled;
        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->propertyChanged('type', $this->type, $type);
        $this->type = $type;
        return $this;
    }

    public function getSkill(): int
    {
        return $this->skill;
    }

    public function setSkill(int $skill): self
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

    public function getWithWorkshop(): bool
    {
        return $this->withWorkshop;
    }

    public function setWithWorkshop(bool $withWorkshop): self
    {
        $this->propertyChanged('withWorkshop', $this->withWorkshop, $withWorkshop);
        $this->withWorkshop = $withWorkshop;

        return $this;
    }

    public function getWorkshopAbstract(): ?string
    {
        return $this->workshopAbstract;
    }

    public function setWorkshopAbstract(?string $workshopAbstract): self
    {
        $this->propertyChanged('workshopAbstract', $this->workshopAbstract, $workshopAbstract);
        $this->workshopAbstract = $workshopAbstract;
        return $this;
    }

    public function getNeedsMentoring(): bool
    {
        return $this->needsMentoring;
    }

    public function setNeedsMentoring(bool $needsMentoring): self
    {
        $this->propertyChanged('needsMentoring', $this->needsMentoring, $needsMentoring);
        $this->needsMentoring = $needsMentoring;

        return $this;
    }

    public function getYoutubeId(): ?string
    {
        return $this->youtubeId;
    }

    public function setYoutubeId(?string $youtubeId): self
    {
        $this->propertyChanged('youtubeId', $this->youtubeId, $youtubeId);
        $this->youtubeId = $youtubeId;

        return $this;
    }

    public function hasYoutubeId(): bool
    {
        return null !== $this->getYoutubeId() && '' !== $this->getYoutubeId();
    }

    public function getYoutubeUrl(): ?string
    {
        if (!$this->hasYoutubeId()) {
            return null;
        }

        return 'https://www.youtube.com/watch?v=' . $this->getYoutubeId();
    }

    public function getSlidesUrl(): ?string
    {
        if (empty($this->slidesUrl)) {
            return null;
        }

        return $this->slidesUrl;
    }

    public function setSlidesUrl(?string $slidesUrl): self
    {
        $this->propertyChanged('slidesUrl', $this->slidesUrl, $slidesUrl);
        $this->slidesUrl = $slidesUrl;

        return $this;
    }

    public function hasSlidesUrl(): bool
    {
        return null !== $this->getSlidesUrl();
    }

    public function getJoindinId(): ?int
    {
        if (0 === $this->joindinId) {
            return null;
        }

        return $this->joindinId;
    }

    public function setJoindinId(?int $joindInId): self
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

    public function getOpenfeedbackPath(): ?string
    {
        return $this->openfeedbackPath;
    }

    public function setOpenfeedbackPath(?string $openfeedbackPath): self
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

    public function getBlogPostUrl(): ?string
    {
        return $this->blogPostUrl;
    }

    public function setBlogPostUrl(?string $blogPostUrl): self
    {
        $this->propertyChanged('blogPostUrl', $this->blogPostUrl, $blogPostUrl);
        $this->blogPostUrl = $blogPostUrl;

        return $this;
    }

    public function hasBlogPostUrl(): bool
    {
        return null !== $this->getBlogPostUrl();
    }

    public function getInterviewUrl(): ?string
    {
        return $this->interviewUrl;
    }

    public function setInterviewUrl(?string $interviewUrl): self
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

    /**
     * @return array<self::TYPE_*, string>
     */
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

    public function getTypeLabel(): string
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

    /**
     * @return array<self::LANGUAGE_CODE_*, string>
     */
    public static function getLanguageLabelsByKey(): array
    {
        return [
            self::LANGUAGE_CODE_FR => 'Français',
            self::LANGUAGE_CODE_EN => 'Anglais',
        ];
    }

    public function getLanguageLabel(): string
    {
        $languageCde = $this->getLanguageCode();
        $mapping = self::getLanguageLabelsByKey();

        if (!isset($mapping[$languageCde])) {
            throw new \Exception(sprintf('Code de langue inconnu : %s', $languageCde));
        }

        return $mapping[$languageCde];
    }

    public function getLanguageCode(): ?string
    {
        return $this->languageCode;
    }

    public function setLanguageCode(?string $languageCode): self
    {
        $this->propertyChanged('languageCode', $this->languageCode, $languageCode);
        $this->languageCode = $languageCode;

        return $this;
    }

    public function getUseMarkdown(): bool
    {
        return $this->useMarkdown;
    }

    public function setUseMarkdown(bool $useMarkdown): self
    {
        $this->propertyChanged('useMarkdown', $this->useMarkdown, $useMarkdown);
        $this->useMarkdown = $useMarkdown;
        return $this;
    }

    public function getVideoHasEnSubtitles(): bool
    {
        return $this->videoHasEnSubtitles;
    }

    public function setVideoHasEnSubtitles(bool $videoHasEnSubtitles): self
    {
        $this->propertyChanged('useMarkdown', $this->videoHasEnSubtitles, $videoHasEnSubtitles);
        $this->videoHasEnSubtitles = $videoHasEnSubtitles;

        return $this;
    }

    public function getVideoHasFrSubtitles(): bool
    {
        return $this->videoHasFrSubtitles;
    }

    public function setVideoHasFrSubtitles(bool $videoHasFrSubtitles): self
    {
        $this->propertyChanged('useMarkdown', $this->videoHasFrSubtitles, $videoHasFrSubtitles);
        $this->videoHasFrSubtitles = $videoHasFrSubtitles;

        return $this;
    }

    public function getTweets(): ?string
    {
        return $this->tweets;
    }

    public function setTweets(?string $tweets): self
    {
        $this->tweets = $tweets;

        return $this;
    }

    public function getTranscript(): ?string
    {
        return $this->transcript;
    }

    public function setTranscript(?string $transcript): self
    {
        $this->transcript = $transcript;

        return $this;
    }

    public function getVerbatim(): ?string
    {
        return $this->verbatim;
    }

    public function setVerbatim(?string $verbatim): self
    {
        $this->verbatim = $verbatim;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getTweetsHasArray(): array
    {
        if (!$this->getTweets()) {
            return [];
        }
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
