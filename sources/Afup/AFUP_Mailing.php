<?php
class AFUP_Mailing
{
    /**
     * Instance de la couche d'abstraction à la base de données
     * @var     object
     * @access  private
     */
    var $_bdd;

    /**
     * Constructeur.
     *
     * @param  object    $bdd   Instance de la couche d'abstraction à la base de données
     * @access public
     * @return void
     */
    function AFUP_Mailing(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    /**
     * Ajoute un email dans la base
     *
     * @param  int      $id         Identifiant du forum
     * @param  string   $champs     Champs à renvoyer
     * @access public
     * @return array
     */
    function AjouterEmail($email, $blacklist = false)
    {
        $blacklist_sql = $blacklist?'1':'0';
        $email =$this->_bdd->echapper($email);
        $requete  = "REPLACE INTO afup_email (`email` ,`blacklist`) ";
        $requete .= "VALUES ($email, $blacklist_sql);";
        return $this->_bdd->executer($requete);
    }

    function BlacklistEmail($email, $blacklist = false)
    {
        return $this->AjouterEmail($email,  true);
    }
    /**
     * Renvoit les informations concernant un forum
     *
     * @param  int      $id         Identifiant du forum
     * @param  string   $champs     Champs à renvoyer
     * @access public
     * @return array
     */
    function obtenirEmails( $blacklist = false)
    {
        $blacklist_sql = $blacklist?'1':'0';
        $requete  = 'SELECT';
        $requete .= '  ' . $champs ;
        $requete .= 'FROM';
        $requete .= '  afup_email ';
        $requete .= 'WHERE blacklist = '.$blacklist_sql;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

	static function envoyerMail($from, $to, $subject,$body, Array $options=array()) {
		require_once dirname(__FILE__).'/AFUP_Configuration.php';
		$configuration = new AFUP_Configuration(dirname(__FILE__).'/../../configs/application/config.php');

		$optionsDefault = array(
			'html' => FALSE,
			'bcc'  => array(),
			'file' => array()); // chaque item doit contenir : (pathFichier, nom fichier)

       $options = array_merge($optionsDefault,$options);

        require_once dirname(__FILE__).'/../../dependencies/phpmailer/class.phpmailer.php';

        $mail = new PHPMailer();
        $mail->IsHTML($options['html']);

        if ($configuration->obtenir('mails|serveur_smtp')) {
            $mail->IsSMTP();
            $mail->Host = $configuration->obtenir('mails|serveur_smtp');
            $mail->SMTPAuth = false;
        }
        if ($configuration->obtenir('mails|tls') == true ) {
            $mail->SMTPAuth = $configuration->obtenir('mails|tls');
            $mail->SMTPSecure = 'tls';
        }
        if ($configuration->obtenir('mails|username')) {
            $mail->Username = $configuration->obtenir('mails|username');
        }
        if ($configuration->obtenir('mails|password')) {
            $mail->Password = $configuration->obtenir('mails|password');
        }
        if ($configuration->obtenir('mails|port')) {
            $mail->Port = $configuration->obtenir('mails|port');
        }
        if ($configuration->obtenir('mails|force_destinataire')) {
            $to = $configuration->obtenir('mails|force_destinataire');
        }

        $bcc = $configuration->obtenir('mails|bcc');
        if ($bcc) {
            $mail->AddBCC($bcc);
        }
        foreach ($options['bcc'] as $valeurBcc) {echo 'ici';
            $mail->AddBCC($valeurBcc);
        }
        foreach ($options['file'] as $filePath) {
            $mail->AddAttachment($filePath[0], $filePath[1]);
            // TODO : deboguer la méthode
        }

        $from_email = is_array($from) ? $from[0]:$from;
        $from_name = (is_array($from) and isset($from[1])) ? $from[1] : '';
        $to_email = is_array($to) ? $to[0] : $to;
        $to_name = (is_array($to) and isset($to[1])) ? $to[1] : '';
        $mail->AddAddress($to_email, $to_name);
        $mail->From = $from_email;
        $mail->FromName = $from_name;
        $mail->Subject = $subject;
        $mail->Body = str_replace('$EMAIL$',$to_email,$body);
        return  $mail->Send();

    }
}
