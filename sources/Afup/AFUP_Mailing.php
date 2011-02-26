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

       $optionsDefault = array(
            'html' => FALSE,
            'bcc'  => array(),
            'file' => array());

       $options = array_merge($optionsDefault,$options);

        require_once 'phpmailer/class.phpmailer.php';

        $mail = new PHPMailer();
        $mail->IsHTML($options['html']);
        
        if ($GLOBALS['conf']->obtenir('mails|serveur_smtp')) {
            $mail->IsSMTP();
            $mail->Host = $GLOBALS['conf']->obtenir('mails|serveur_smtp');
            $mail->SMTPAuth = false;
        }
        if ($GLOBALS['conf']->obtenir('mails|tls') == true ) {
            $mail->SMTPAuth = $GLOBALS['conf']->obtenir('mails|tls');
            $mail->SMTPSecure = 'tls';
        }
        if ($GLOBALS['conf']->obtenir('mails|username')) {
            $mail->Username = $GLOBALS['conf']->obtenir('mails|username');
        }
        if ($GLOBALS['conf']->obtenir('mails|password')) {
            $mail->Password = $GLOBALS['conf']->obtenir('mails|password');
        }
        if ($GLOBALS['conf']->obtenir('mails|port')) {
            $mail->Port = $GLOBALS['conf']->obtenir('mails|port');
        }
        if ($GLOBALS['conf']->obtenir('mails|force_destinataire')) {
            $to = $GLOBALS['conf']->obtenir('mails|force_destinataire');
        }

        //Gestion BCC
        $mail->AddBCC($GLOBALS['conf']->obtenir('mails|bcc'));
        foreach ($options['bcc'] as $valeurBcc) {
            $mail->AddBCC($valeurBcc);
        }

        //Gestion Attachement
        foreach ($options['file'] as $filePath) {
            $mail->AddAttachment($filePath);
        }
        
        $from_email = is_array($from)?$from[0]:$from;
        $from_name = is_array($from)?$from[1]:'';
        $to_email = is_array($to)?$to[0]:$to;
        $to_name = is_array($to)?$to[1]:'';
        $mail->AddAddress($to_email, $to_name);
        $mail->From     = $from_email;
        $mail->FromName = $from_name;        
        $mail->Subject  = $subject;
        $mail->Body     = str_replace('$EMAIL$',$to_email,$body);
        return  $mail->Send();

    }

}
?>