<?php

declare(strict_types=1);

namespace AppBundle\Event\Entity;

use AppBundle\Event\Entity\Repository\GithubUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: GithubUserRepository::class)]
#[ORM\Table(name: 'afup_user_github')]
class GithubUser implements UserInterface, EquatableInterface, \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'github_id', options: ['unsigned' => true])]
    private int $githubId = 0;

    #[ORM\Column(length: 255)]
    private string $login = '';

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $company = null;

    #[ORM\Column(name: 'profile_url', length: 255)]
    private string $profileUrl = '';

    #[ORM\Column(name: 'avatar_url', length: 255)]
    private string $avatarUrl = '';

    #[ORM\Column(name: 'afup_crew', options: ['unsigned' => true, 'default' => 0])]
    private bool $afupCrew = false;

    public function __toString(): string
    {
        return $this->getLabel();
    }

    /**
     * @param array{login: string, id: int, avatar_url: string, company: ?string, name: ?string, html_url: string} $apiData Données renvoyées par l'API GitHub (/users/{login})
     */
    public static function fromApi(array $apiData): self
    {
        return new self()
            ->setLogin($apiData['login'])
            ->setGithubId($apiData['id'])
            ->setAvatarUrl($apiData['avatar_url'])
            ->setCompany($apiData['company'])
            ->setName($apiData['name'])
            ->setProfileUrl($apiData['html_url']);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getLabel(): string
    {
        $label = $this->login;
        if ($this->name) {
            $label .= " ({$this->name})";
        }

        return $label;
    }

    public function getGithubId(): int
    {
        return $this->githubId;
    }

    public function setGithubId(int $githubId): self
    {
        $this->githubId = $githubId;

        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->company = $company;

        return $this;
    }

    public function getProfileUrl(): string
    {
        return $this->profileUrl;
    }

    public function setProfileUrl(string $profileUrl): self
    {
        $this->profileUrl = $profileUrl;

        return $this;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(string $avatarUrl): self
    {
        $this->avatarUrl = $avatarUrl;

        return $this;
    }

    public function getAfupCrew(): bool
    {
        return $this->afupCrew;
    }

    public function setAfupCrew(bool $afupCrew): self
    {
        $this->afupCrew = $afupCrew;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = ['ROLE_GITHUB'];
        if ($this->afupCrew) {
            $roles[] = 'ROLE_AFUP_CREW';
        }

        return $roles;
    }

    public function getUserIdentifier(): string
    {
        if ($this->login === '') {
            throw new \LogicException('A GitHub user must have a login to be identified.');
        }

        return $this->login;
    }

    public function eraseCredentials(): void {}

    /**
     * Seuls l'id et le login sont stockés en session : le user provider recharge l'entité complète.
     *
     * @return array{id: ?int, login: string}
     */
    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
        ];
    }

    /**
     * @param array{id: ?int, login: string} $data
     */
    public function __unserialize(array $data): void
    {
        $this->id = $data['id'];
        $this->login = $data['login'];
    }

    public function isEqualTo(UserInterface $user): bool
    {
        return $user instanceof self && $user->getId() === $this->id;
    }
}
