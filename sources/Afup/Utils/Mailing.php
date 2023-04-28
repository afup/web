<?php
namespace Afup\Site\Utils;



use AppBundle\Email\Mailer\Message;

class Mailing
{
    /**
     * Instance de la couche d'abstraction à la base de données
     * @var     \Afup\Site\Utils\Base_De_Donnees
     * @access  private
     */
    var $_bdd;

    /**
     * Constructeur.
     *
     * @param  object $bdd Instance de la couche d'abstraction à la base de données
     * @access public
     * @return void
     */
    function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    /**
     * Ajoute un email dans la base
     *
     * @param  int $id Identifiant du forum
     * @param  string $champs Champs à renvoyer
     * @access public
     * @return array
     */
    function AjouterEmail($email, $blacklist = false)
    {
        $blacklist_sql = $blacklist ? '1' : '0';
        $email = $this->_bdd->echapper($email);
        $requete = "REPLACE INTO afup_email (`email` ,`blacklist`) ";
        $requete .= "VALUES ($email, $blacklist_sql);";
        return $this->_bdd->executer($requete);
    }

    function BlacklistEmail($email, $blacklist = false)
    {
        return $this->AjouterEmail($email, true);
    }

    /**
     * Renvoit les informations concernant un forum
     *
     * @param  int $id Identifiant du forum
     * @param  string $champs Champs à renvoyer
     * @access public
     * @return array
     */
    function obtenirEmails($blacklist = false)
    {
        $blacklist_sql = $blacklist ? '1' : '0';
        $requete = 'SELECT';
        $requete .= '  ';
        $requete .= 'FROM';
        $requete .= '  afup_email ';
        $requete .= 'WHERE blacklist = ' . $blacklist_sql;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    public static function envoyerMail(Message $message, $body)
    {
        $recipients = $message->getRecipients();
        $recipient = reset($recipients);
        $message->setContent(str_replace('$EMAIL$', $recipient->getEmail(), $body));

        return Mail::createMailer()->send($message);
    }
}
