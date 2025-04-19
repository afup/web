<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GithubUser implements NotifyPropertyInterface, UserInterface, EquatableInterface
{
    use NotifyProperty;

    private ?int $id = null;

    private ?int $githubId = null;

    private string $login = '';

    private string $name = '';

    private ?string $company = null;

    private string $profileUrl = '';

    private string $avatarUrl = '';

    private bool $afupCrew = false;

    public function __toString()
    {
        return $this->getLabel();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): string
    {
        $label = $this->login;
        if ($this->name) {
            $label .= " ({$this->name})";
        }

        return $label;
    }

    public static function fromApi(array $apiData): self
    {
        $githubUser = new self();
        $githubUser->setLogin($apiData['login']);
        $githubUser->setGithubId($apiData['id']);
        $githubUser->setAvatarUrl($apiData['avatar_url']);
        $githubUser->setCompany($apiData['company']);
        $githubUser->setName($apiData['name']);
        $githubUser->setProfileUrl($apiData['html_url']);

        return $githubUser;
    }

    public function setId(int $id): self
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    public function getGithubId(): ?int
    {
        return $this->githubId;
    }

    public function setGithubId(int $githubId): self
    {
        $this->propertyChanged('githubId', $this->githubId, $githubId);
        $this->githubId = $githubId;
        return $this;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->propertyChanged('login', $this->login, $login);
        $this->login = $login;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->propertyChanged('name', $this->name, $name);
        $this->name = $name;
        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): self
    {
        $this->propertyChanged('company', $this->company, $company);
        $this->company = $company;
        return $this;
    }

    public function getProfileUrl(): string
    {
        return $this->profileUrl;
    }

    public function setProfileUrl(string $profileUrl): self
    {
        $this->propertyChanged('profileUrl', $this->profileUrl, $profileUrl);
        $this->profileUrl = $profileUrl;
        return $this;
    }

    public function getAvatarUrl(): string
    {
        return $this->avatarUrl;
    }

    public function setAvatarUrl(string $avatarUrl): self
    {
        $this->propertyChanged('avatarUrl', $this->avatarUrl, $avatarUrl);
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
        if ($this->afupCrew === true) {
            $roles[] = 'ROLE_AFUP_CREW';
        }
        return $roles;
    }

    public function getPassword(): ?string
    {
        return null;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->getLogin();
    }

    /**
     *
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->getLogin();
    }

    public function eraseCredentials(): void
    {
    }

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
        ];
    }

    public function __unserialize($serialized): void
    {
        $this->id = $serialized['id'];
        $this->login = $serialized['login'];
    }

    public function isEqualTo(UserInterface $user): bool
    {
        /**
         * @var self $user
         */
        return ($user->getId() === $this->id);
    }
}
