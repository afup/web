<?php
namespace Afup\Site\Utils;



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
        $requete .= '  ' . $champs;
        $requete .= 'FROM';
        $requete .= '  afup_email ';
        $requete .= 'WHERE blacklist = ' . $blacklist_sql;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    static function envoyerMail($from, $to, $subject, $body, Array $options = array())
    {
        $configuration = new Configuration(dirname(__FILE__) . '/../../../configs/application/config.php');

        $paramsDefault = array(
            'html' => false,
            'bcc_address' => array(),
            'attachments' => array()); // chaque item doit contenir : (pathFichier, nom fichier)

        $parameters = array_merge($paramsDefault, $options);

        $mail = new Mail(null, null);

        $parameters['from'] = [
            'email' => is_array($from) ? $from[0] : $from,
            'name' => (is_array($from) and isset($from[1])) ? $from[1] : '',
        ];
        $parameters['subject'] = $subject;
        $toArray = [
                [
                'name' => (is_array($to) and isset($to[1])) ? $to[1] : '',
                'email' => is_array($to) ? $to[0] : $to,
            ]
        ];

        $body = str_replace('$EMAIL$', $toArray[0]['email'], $body);

        return $mail->send($body, $toArray, [], $parameters);
    }
}
