<?php

/**
 * Modèle de données représentant un message contact à envoyer.
 * 
 * @todo       Déplacer cette classe dans les modèles de données de l'architecture MVC lorsque celle-ci sera implémentée
 * @copyright  2006 Guillaume Ponçon - all rights reserved
 * @license    http://www.zend.com/license/3_0.txt   PHP License 3.0
 * @version    Release: @1.0.0@
 * @since      Class available since Release 1.0.0
 * @author     Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @package    afup
 * @subpackage directory
 */
class Afup_Directory_Contact extends Fdap_Model
{

    /**
     * Email de l'expéditeur
     */
    private $mail1;

    /**
     * Email de confirmation
     */
    private $mail2;

    /**
     * Message à envoyer
     */
    private $message;

    /**
     * Modèle de l'entreprise à contacter
     * 
     * @var Afup_Directory_Member
     */
    private $member;

    /**
     * Construction d'une instance du modèle
     *
     * @param array $post
     */
    public function __construct($post = null)
    {
        if ($post !== null) {
            $this->setMail1($post['email1']);
            $this->setMail2($post['email2']);
            $this->setMessage($post['message']);
        }
    }

    /*
    * Getters & Setters
    */

    public final function getMail1()
    {
        return $this->mail1;
    }

    public final function setMail1($mail1)
    {
        $this->mail1 = trim((string) $mail1);
        return true;
    }

    public final function getMail2()
    {
        return $this->mail2;
    }

    public final function setMail2($mail2)
    {
        $this->mail2 = trim((string) $mail2);
        return true;
    }

    public final function getMessage()
    {
        return $this->message;
    }

    public final function setMessage($message)
    {
        $this->message = (string) $message;
        return true;
    }

    /**
     * @return Afup_Directory_Member
     */
    public final function getMember()
    {
        return $this->member;
    }

    public final function setMember(Afup_Directory_Member $member)
    {
        $this->member = $member;
        return true;
    }

    /**
     * Validation des données du modèle
     *
     * @return Fdap_Model_Errors
     */
    public function validate()
    {
        $errors = new Fdap_Model_Errors();
        if (!preg_match('/^[^@ ]+@[a-zA-Z._-]+\.[a-z]{2,5}$/', $this->mail1)) {
            $errors->addNewError('contact[email1]', "La saisie d'un email valide est obligatoire.");
        } elseif($this->mail1 !== $this->mail2) {
            $errors->addNewError('contact[email1]', "Les deux emails saisis ne correspondent pas.");
        }
        if (!$this->message) {
            $errors->addNewError('contact[message]', "La saisie d'un message est obligatoire");
        }
        return $errors;
    }

    /**
     * Renvoi des données sous forme de tableau pour sérialisation
     *
     * @return array
     */
    public function toArray()
    {
        $data = array();
        $data['from']       = $this->getMail1();
        $data['message']    = str_replace("\n", '###', $this->getMessage());
        $data['to']         = $this->member->getEmail();
        $data['enterprise'] = $this->member->getRaisonSociale();
        return $data;
    }

        
    /**
     * Renvoit l'objet de manipulation des données du modèle
     *
     * @return Fdap_Model_Request
     */
    public function getModelRequest()
    {
        return new Fdap_Model_Request($this);
    }
    
    /*
     * Sérialisation des attributs
     *
     * @return array
     */
    /*public function __sleep()
    {
    $keys = array();
    foreach ($this as $aKey => $aValue) {
    $keys[] = $aKey;
    if (is_object($aValue)) {
    $aValue = serialize($aValue);
    }
    $this->${'public' . $aKey} = $aValue;
    }
    return array($keys);
    }*/

    /*
     * Désérialisation des attributs
     */
    /* public function __wakeup()
    {
    $member = new Afup_Directory_Member();
    $member = unserialize($this->member);
    $this->member = $member;
    }*/

}