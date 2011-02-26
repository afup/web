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


   public static function envoyerMail($from, $to, $subject,$body,$is_html =false) {
        return(self::envoyeMail($from, $to, $subject,$body,$is_html));
    }

   function envoyeMail($from, $to, $subject,$body,$is_html =false) {

        require_once 'phpmailer/class.phpmailer.php';

        $mail = new PHPMailer;
        $mail->IsHTML($is_html);
        if ($GLOBALS['conf']->obtenir('mails|serveur_smtp')) {
            $mail->IsSMTP();
            $mail->Host = $GLOBALS['conf']->obtenir('mails|serveur_smtp');
            $mail->SMTPAuth = false;
        }
        //$personne_physique['email'] = 'xgorse@elao.com';
        $from_email = is_array($from)?$from[0]:$from;
        $from_name = is_array($from)?$from[1]:'';
        $to_email = is_array($to)?$to[0]:$to;
        $to_name = is_array($to)?$to[1]:'';
        $mail->AddAddress($to_email, $to_name);
        $mail->From     = $from_email;
        $mail->FromName = $from_name;
        $mail->BCC      = $GLOBALS['conf']->obtenir('mails|email_expediteur');
        $mail->Subject  = $subject;
        $mail->Body     = str_replace('$EMAIL$',$to_email,$body);
        //var_dump($mail);die;
           return  $mail->Send();

    }

}
?>