<?php

declare(strict_types=1);

namespace AppBundle\Event\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class GithubUser implements NotifyPropertyInterface, UserInterface, \Serializable, EquatableInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $githubId;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $company;

    /**
     * @var string
     */
    private $profileUrl;

    /**
     * @var string
     */
    private $avatarUrl;

    /**
     * @var bool
     */
    private $afupCrew = false;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getLabel()
    {
        $label = $this->login;
        if (null !== $this->name) {
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

    /**
     * @param int $id
     */
    public function setId($id): self
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getGithubId()
    {
        return $this->githubId;
    }

    /**
     * @param int $githubId
     */
    public function setGithubId($githubId): self
    {
        $this->propertyChanged('githubId', $this->githubId, $githubId);
        $this->githubId = $githubId;
        return $this;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param string $login
     */
    public function setLogin($login): self
    {
        $this->propertyChanged('login', $this->login, $login);
        $this->login = $login;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name): self
    {
        $this->propertyChanged('name', $this->name, $name);
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany($company): self
    {
        $this->propertyChanged('company', $this->company, $company);
        $this->company = $company;
        return $this;
    }

    /**
     * @return string
     */
    public function getProfileUrl()
    {
        return $this->profileUrl;
    }

    /**
     * @param string $profileUrl
     */
    public function setProfileUrl($profileUrl): self
    {
        $this->propertyChanged('profileUrl', $this->profileUrl, $profileUrl);
        $this->profileUrl = $profileUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getAvatarUrl()
    {
        return $this->avatarUrl;
    }

    /**
     * @param string $avatarUrl
     */
    public function setAvatarUrl($avatarUrl): self
    {
        $this->propertyChanged('avatarUrl', $this->avatarUrl, $avatarUrl);
        $this->avatarUrl = $avatarUrl;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getAfupCrew()
    {
        return $this->afupCrew;
    }

    /**
     * @param boolean $afupCrew
     */
    public function setAfupCrew($afupCrew): self
    {
        $this->afupCrew = $afupCrew;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        $roles = ['ROLE_GITHUB'];
        if ($this->afupCrew === true) {
            $roles[] = 'ROLE_AFUP_CREW';
        }
        return $roles;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->getLogin();
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials(): void
    {
    }
    /***************
     * Serializable
     **************/


    /**
     * @inheritDoc
     */
    public function serialize()
    {
        return serialize(['id' => $this->id]);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized): void
    {
        $user = unserialize($serialized);
        $this->id = $user['id'];
    }

    /***************
     * EquatableInterface
     ***************/

    /**
     * @inheritDoc
     */
    public function isEqualTo(UserInterface $user)
    {
        /**
         * @var self $user
         */
        return ($user->getId() === $this->id);
    }
}
