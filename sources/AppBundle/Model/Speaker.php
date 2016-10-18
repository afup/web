<?php


namespace AppBundle\Model;


use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Speaker implements NotifyPropertyInterface
{
    use NotifyProperty;
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $eventId;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\GreaterThan(0)
     */
    private $user;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    private $civility;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    private $firstname;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    private $lastname;

    /**
     * @Assert\NotBlank()
     * @Assert\Email()
     * @var string
     */
    private $email;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    private $company;

    /**
     * @Assert\NotBlank()
     * @var string
     */
    private $biography;

    /**
     * @var string
     */
    private $twitter;

    /**
     * @var GithubUser
     */
    private $githubUser;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Speaker
     */
    public function setId($id)
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
        return $this;
    }

    /**
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param int $user
     * @return Speaker
     */
    public function setUser($user)
    {
        $this->propertyChanged('user', $this->user, $user);
        $this->user = $user;
        return $this;
    }

    /**
     * @return int
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * @param int $eventId
     * @return Speaker
     */
    public function setEventId($eventId)
    {
        $this->propertyChanged('eventId', $this->eventId, $eventId);
        $this->eventId = $eventId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCivility()
    {
        return $this->civility;
    }

    /**
     * @param string $civility
     * @return Speaker
     */
    public function setCivility($civility)
    {
        $this->propertyChanged('civility', $this->civility, $civility);
        $this->civility = $civility;
        return $this;
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param string $firstname
     * @return Speaker
     */
    public function setFirstname($firstname)
    {
        $this->propertyChanged('firstname', $this->firstname, $firstname);
        $this->firstname = $firstname;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param string $lastname
     * @return Speaker
     */
    public function setLastname($lastname)
    {
        $this->propertyChanged('lastname', $this->lastname, $lastname);
        $this->lastname = $lastname;
        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return Speaker
     */
    public function setEmail($email)
    {
        $this->propertyChanged('email', $this->email, $email);
        $this->email = $email;
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
     * @return Speaker
     */
    public function setCompany($company)
    {
        $this->propertyChanged('company', $this->company, $company);
        $this->company = $company;
        return $this;
    }

    /**
     * @return string
     */
    public function getBiography()
    {
        return $this->biography;
    }

    /**
     * @param string $biography
     * @return Speaker
     */
    public function setBiography($biography)
    {
        $this->propertyChanged('biography', $this->biography, $biography);
        $this->biography = $biography;
        return $this;
    }

    /**
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * @param string $twitter
     * @return Speaker
     */
    public function setTwitter($twitter)
    {
        $this->propertyChanged('twitter', $this->twitter, $twitter);
        $this->twitter = $twitter;
        return $this;
    }

    /**
     * @return GithubUser
     */
    public function getGithubUser()
    {
        return $this->githubUser;
    }

    /**
     * @param GithubUser $githubUser
     * @return Speaker
     */
    public function setGithubUser(GithubUser $githubUser)
    {
        $this->githubUser = $githubUser;
        return $this;
    }
}
