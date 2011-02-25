<?php


class AFUP_Compta_Facture
{
    var $_bdd;
    
    function AFUP_Compta_Facture(&$bdd)
    {
        $this->_bdd = $bdd;   
    }  

    
    /* Journal des opération
     * 
     */
	function obtenirDevis() 
    {

		$requete  = 'SELECT ';
		$requete .= ' compta_facture.* '; 
		$requete .= 'FROM  ';
		$requete .= ' compta_facture  ';
		$requete .= 'WHERE  ';
		$requete .= ' numero_devis != "" ';
		$requete .= 'ORDER BY ';
		$requete .= ' compta_facture.date_ecriture ';

		return $this->_bdd->obtenirTous($requete);
    }
    
	function obtenirDevisDetails($id) 
    {

		$requete  = 'SELECT ';
		$requete .= 'compta_facture.*, ';
		$requete .= 'compta_facture_details.ref,compta_facture_details.designation,compta_facture_details.quantite,compta_facture_details.pu '; 
		$requete .= 'FROM  ';
		$requete .= 'compta_facture,  ';
		$requete .= 'compta_facture_details ';
		$requete .= 'WHERE  ';
		$requete .= ' numero_devis != "" ';
		$requete .= 'compta_facture.id = compta_facture_details.idcompta_facture ';
		$requete .= 'ORDER BY ';
		$requete .= 'compta.date_ecriture ';
	
		return $this->_bdd->obtenirTous($requete);
    }
    
	function obtenirFacture() 
    {

		$requete  = 'SELECT ';
		$requete .= ' compta_facture.* '; 
		$requete .= 'FROM  ';
		$requete .= 'compta_facture  ';
		$requete .= 'WHERE  ';
		$requete .= ' numero_facture != "" ';
		$requete .= 'ORDER BY ';
		$requete .= 'compta_facture.date_ecriture ';

		return $this->_bdd->obtenirTous($requete);
    }
    
	function obtenirFactureDetails($id) 
    {

		$requete  = 'SELECT ';
		$requete .= 'compta_facture.*, ';
		$requete .= 'compta_facture_details.ref,compta_facture_details.designation,compta_facture_details.quantite,compta_facture_details.pu '; 
		$requete .= 'FROM  ';
		$requete .= 'compta_facture,  ';
		$requete .= 'compta_facture_details ';
		$requete .= 'WHERE  ';
		$requete .= ' numero_facture != "" ';
		$requete .= 'compta_facture.id = compta_facture_details.idcompta_facture ';
		$requete .= 'ORDER BY ';
		$requete .= 'compta.date_ecriture ';
	
		return $this->_bdd->obtenirTous($requete);
    }
    
    function obtenir($id) 
    {
        $requete  = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  compta_facture ';
        $requete .= 'WHERE id=' . $id;

        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function obtenir_details($id) 
    {
        $requete  = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  compta_facture_details ';
        $requete .= 'WHERE idcompta_facture=' . $id;
		
        return $this->_bdd->obtenirTous($requete);
    }
    
    function ajouter($date_ecriture,$societe,$service,$adresse,$code_postal,$ville,$id_pays,
					$nom,$prenom,$tel,$email,$observation,$ref_clt1,$ref_clt2,$ref_clt3)
	{
	
		$requete = 'INSERT INTO ';
		$requete .= 'compta_facture (';
		$requete .= 'date_ecriture,societe,service,adresse,code_postal,ville,id_pays,';
		$requete .= 'nom,prenom,tel,';
		$requete .= 'email,observation,ref_clt1,ref_clt2,ref_clt3,numero_devis) ';
		$requete .= 'VALUES (';
		$requete .= $this->_bdd->echapper($date_ecriture) . ',';
		$requete .= $this->_bdd->echapper($societe) . ',';
		$requete .= $this->_bdd->echapper($service) . ',';
		$requete .= $this->_bdd->echapper($adresse) . ',';
		$requete .= $this->_bdd->echapper($code_postal) . ',';
		$requete .= $this->_bdd->echapper($ville) . ',';
		$requete .= $this->_bdd->echapper($id_pays) . ',';
		$requete .= $this->_bdd->echapper($nom) . ',';
		$requete .= $this->_bdd->echapper($prenom) . ',';
		$requete .= $this->_bdd->echapper($tel) . ',';
		$requete .= $this->_bdd->echapper($email) . ',';
		$requete .= $this->_bdd->echapper($observation) . ',';
		$requete .= $this->_bdd->echapper($ref_clt1) . ',';
		$requete .= $this->_bdd->echapper($ref_clt2) . ',';
		$requete .= $this->_bdd->echapper($ref_clt3) . ', ';
		$requete .= $this->genererNumeroDevis(). ' ';
		$requete .= ');';

		return $this->_bdd->executer($requete);
	}

	function ajouter_details($ref,$designation,$quantite,$pu)
	{
		$requete = 'INSERT INTO ';
		$requete .= 'compta_facture_details (';
		$requete .= 'idcompta_facture,ref,designation,quantite,pu) ';
		$requete .= 'VALUES (';
		$requete .= $this->obtenirDernier() .','; 
		$requete .= $this->_bdd->echapper($ref) . ',';
		$requete .= $this->_bdd->echapper($designation) . ',';
		$requete .= $this->_bdd->echapper($quantite) . ',';
		$requete .= $this->_bdd->echapper($pu) . ' ';
		$requete .= ');';

		return $this->_bdd->executer($requete);
	}
	
	function modifier($id,$date_ecriture,$societe,$service,$adresse,$code_postal,$ville,$id_pays,
					$nom,$prenom,$tel,$email,$observation,$ref_clt1,$ref_clt2,$ref_clt3,
					$numero_devis,$numero_facture)
	{
	
		$requete = 'UPDATE ';
		$requete .= 'compta_facture ';
		$requete .= 'SET ';
		$requete .= 'date_ecriture='.$this->_bdd->echapper($date_ecriture) . ',';
		$requete .= 'societe='.$this->_bdd->echapper($societe) . ',';
		$requete .= 'service='.$this->_bdd->echapper($service) . ',';
		$requete .= 'adresse='.$this->_bdd->echapper($adresse) . ',';
		$requete .= 'code_postal='.$this->_bdd->echapper($code_postal) . ',';
		$requete .= 'ville='.$this->_bdd->echapper($ville) . ',';
		$requete .= 'id_pays='.$this->_bdd->echapper($id_pays) . ',';
		$requete .= 'nom='.$this->_bdd->echapper($nom) . ',';
		$requete .= 'prenom='.$this->_bdd->echapper($prenom) . ',';
		$requete .= 'tel='.$this->_bdd->echapper($tel) . ',';
		$requete .= 'email='.$this->_bdd->echapper($email) . ',';
		$requete .= 'observation='.$this->_bdd->echapper($observation) . ', ';
		$requete .= 'ref_clt1='.$this->_bdd->echapper($ref_clt1) . ',';
		$requete .= 'ref_clt2='.$this->_bdd->echapper($ref_clt2) . ',';
		$requete .= 'ref_clt3='.$this->_bdd->echapper($ref_clt3) . ', ';
		$requete .= 'numero_devis='.$this->_bdd->echapper($numero_devis) . ' ';
		$requete .= 'numero_facture='.$this->_bdd->echapper($numero_facture) . ' ';
		$requete .= 'WHERE ';
		$requete .= 'id=' . $id. ' ';

		return $this->_bdd->executer($requete);
	}

	function modifier_details($id,$ref,$designation,$quantite,$pu)
	{
		$requete = 'UPDATE ';
		$requete .= 'compta_facture_details ';
		$requete .= 'SET ';
		$requete .= 'ref='.$this->_bdd->echapper($ref) . ',';
		$requete .= 'designation='.$this->_bdd->echapper($designation) . ',';
		$requete .= 'quantite='.$this->_bdd->echapper($quantite) . ',';
		$requete .= 'pu='.$this->_bdd->echapper($pu) . ' ';
		$requete .= 'WHERE ';
		$requete .= 'id=' . $id. ' ';

		return $this->_bdd->executer($requete);
	}
		
    function obtenirDernier()
    {
        $requete  = 'SELECT MAX(id)';
        $requete .= 'FROM';
        $requete .= '  compta_facture ';

        return $this->_bdd->obtenirUn($requete);
    }



   function genererNumeroDevis()
    {
    	// afup_cotisations
        $requete  = 'SELECT';
        $requete .= "  MAX(CAST(SUBSTRING_INDEX(numero_devis, '-', -1) AS UNSIGNED)) + 1 ";
        $requete .= 'FROM';
        $requete .= ' compta_facture ';
        $requete .= 'WHERE';
        $requete .= '  LEFT(numero_devis, 4)=' . $this->_bdd->echapper(date('Y'));
        $index = $this->_bdd->obtenirUn($requete);
        return date('Y') . '-' . (is_null($index) ? 1 : $index);
    }

    function genererDevis($reference, $chemin = null)
    {
        $requete    = 'SELECT * FROM compta_facture WHERE numero_devis=' . $this->_bdd->echapper($reference);
        $coordonnées = $this->_bdd->obtenirEnregistrement($requete);

        $requete    = 'SELECT * FROM compta_facture_details WHERE idcompta_facture=' . $this->_bdd->echapper($coordonnées['id']);
        $details = $this->_bdd->obtenirTous($requete);

        require_once 'Afup/AFUP_Configuration.php';
        $configuration = $GLOBALS['AFUP_CONF'];

        require_once 'Afup/AFUP_Pays.php';
        $pays = new AFUP_Pays($this->_bdd);

        // Construction du PDF
        require_once 'Afup/AFUP_PDF_Facture.php';
        $pdf = new AFUP_PDF_Facture($configuration);
        $pdf->AddPage();

        // Haut de page [afup]
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(130, 5, 'AFUP');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(60, 5, $configuration->obtenir('afup|raison_sociale'));
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(130, 5, utf8_decode('Association Française des Utilisateurs de PHP'));
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(60, 5, utf8_decode($configuration->obtenir('afup|adresse')));
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(130, 5, 'http://www.afup.org');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(130, 5, 'SIRET : '. $configuration->obtenir('afup|siret'));
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(60, 5, $configuration->obtenir('afup|code_postal') . ' ' . utf8_decode($configuration->obtenir('afup|ville')));
        $pdf->Ln();
        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Email : ' . $configuration->obtenir('afup|email'));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . date('d/m/Y', (isset($facture['date_facture']) && !empty($facture['date_facture']) ? $facture['date_facture'] : time())));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();


        // A l'attention du client [adresse]
         $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(10);
        $pdf->setx(120);
        $pdf->MultiCell(130, 5, utf8_decode($coordonnées['societe']). "\n" . 
        				utf8_decode($coordonnées['service']) . "\n" . 
        				utf8_decode($coordonnées['adresse']) . "\n" . 
        				utf8_decode($coordonnées['code_postal']) . "\n" . 
        				utf8_decode($coordonnées['ville']) ."\n".       				
        				utf8_decode($pays->obtenirNom($coordonnées['id_pays'])));

        $pdf->Ln(10);
       $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(0, 5, utf8_decode('Devis n° ' . $reference),0,0,"C");
         $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(15);
      	$pdf->Cell(40, 5, utf8_decode('Repère(s) : '));
        if ($coordonnées['ref_clt1']) { 
        	$pdf->setx(30);
        	$pdf->Cell(100, 5, utf8_decode($coordonnées['ref_clt1']));
        	$pdf->Ln(5);
        }
        if ($coordonnées['ref_clt2']) {         
        	$pdf->setx(30);
        	$pdf->Cell(100, 5, utf8_decode( $coordonnées['ref_clt2']));
        	$pdf->Ln(5);
        }
        if ($coordonnées['ref_clt3']) {      
        	$pdf->setx(30);
        	$pdf->Cell(100, 5, utf8_decode( $coordonnées['ref_clt3']));
        	$pdf->Ln(5);
        }
        $pdf->Ln(10);
        
        $pdf->MultiCell(180, 5, utf8_decode("Comme convenu, nous vous prions de trouver votre devis"));
        // Cadre
        $pdf->Ln(5);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(30, 5, 'Type', 1, 0, 'L', 1);
        $pdf->Cell(80, 5, 'Description', 1, 0, 'L', 1);
        $pdf->Cell(20, 5, 'Quantite', 1, 0, 'L', 1);
        $pdf->Cell(30, 5, 'Prix', 1, 0, 'L', 1);
        $pdf->Cell(30, 5, 'Total', 1, 0, 'L', 1);
        
        $total = 0;
        foreach ($details as $detail) {
			$montant=$detail['quantite']*$detail['pu'];
        	
        	$pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);

            $pdf->Cell(30, 5, $detail['ref'], 1);
            $pdf->Cell(80, 5, utf8_decode($detail['designation']) , 1);
            $pdf->Cell(20, 5, utf8_decode($detail['quantite']), 1,0,"C");
            $pdf->Cell(30, 5, utf8_decode($detail['pu']) . utf8_decode(' '), 1,0,"R");
            $pdf->Cell(30, 5, utf8_decode($montant) . utf8_decode(' '), 1,0,"R");
                       
            $total += $montant;
        }

        $pdf->Ln();
        $pdf->SetFillColor(225, 225, 225);
        $pdf->Cell(160, 5, 'TOTAL', 1, 0, 'L', 1);
        $pdf->Cell(30, 5, $total . utf8_decode(' '), 1, 0, 'R', 1);

        $pdf->Ln(15);
        $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');
        $pdf->Ln(10);
        $pdf->Cell(10, 5, 'Observations : ');
        $pdf->Ln(5);
        $pdf->Cell(130, 5, $coordonnées['observation'], 0, 0, 'L', 0);
         
        if (is_null($chemin)) {
            $pdf->Output('devis.pdf', 'D');
        } else {
            $pdf->Output($chemin, 'F');
        }
    }


    function genererFacture($reference, $chemin = null)
    {
            $requete    = 'SELECT * FROM compta_facture WHERE numero_devis=' . $this->_bdd->echapper($reference);
        $coordonnées = $this->_bdd->obtenirEnregistrement($requete);

        $requete    = 'SELECT * FROM compta_facture_details WHERE idcompta_facture=' . $this->_bdd->echapper($coordonnées['id']);
        $details = $this->_bdd->obtenirTous($requete);

        require_once 'Afup/AFUP_Configuration.php';
        $configuration = $GLOBALS['AFUP_CONF'];

        require_once 'Afup/AFUP_Pays.php';
        $pays = new AFUP_Pays($this->_bdd);

        // Construction du PDF
        require_once 'Afup/AFUP_PDF_Facture.php';
        $pdf = new AFUP_PDF_Facture($configuration);
        $pdf->AddPage();

        // Haut de page [afup]
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(130, 5, 'AFUP');
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(60, 5, $configuration->obtenir('afup|raison_sociale'));
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(130, 5, utf8_decode('Association Française des Utilisateurs de PHP'));
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(60, 5, utf8_decode($configuration->obtenir('afup|adresse')));
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(130, 5, 'http://www.afup.org');
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(130, 5, 'SIRET : '. $configuration->obtenir('afup|siret'));
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(60, 5, $configuration->obtenir('afup|code_postal') . ' ' . utf8_decode($configuration->obtenir('afup|ville')));
        $pdf->Ln();
        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Email : ' . $configuration->obtenir('afup|email'));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();
        $pdf->Cell(130, 5);
        $pdf->Cell(60, 5, 'Le ' . date('d/m/Y', (isset($facture['date_facture']) && !empty($facture['date_facture']) ? $facture['date_facture'] : time())));

        $pdf->Ln();
        $pdf->Ln();
        $pdf->Ln();


        // A l'attention du client [adresse]
         $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(10);
        $pdf->setx(120);
        $pdf->MultiCell(130, 5, utf8_decode($coordonnées['societe']). "\n" . 
        				utf8_decode($coordonnées['service']) . "\n" . 
        				utf8_decode($coordonnées['adresse']) . "\n" . 
        				utf8_decode($coordonnées['code_postal']) . "\n" . 
        				utf8_decode($coordonnées['ville']) ."\n".       				
        				utf8_decode($pays->obtenirNom($coordonnées['id_pays'])));

        $pdf->Ln(10);
       $pdf->SetFont('Arial', 'BU', 10);
        $pdf->Cell(0, 5, utf8_decode('Devis n° ' . $reference),0,0,"C");
         $pdf->SetFont('Arial', '', 10);
        $pdf->Ln(15);
      	$pdf->Cell(40, 5, utf8_decode('Repère(s) : '));
        if ($coordonnées['ref_clt1']) { 
        	$pdf->setx(30);
        	$pdf->Cell(100, 5, utf8_decode($coordonnées['ref_clt1']));
        	$pdf->Ln(5);
        }
        if ($coordonnées['ref_clt2']) {         
        	$pdf->setx(30);
        	$pdf->Cell(100, 5, utf8_decode( $coordonnées['ref_clt2']));
        	$pdf->Ln(5);
        }
        if ($coordonnées['ref_clt3']) {      
        	$pdf->setx(30);
        	$pdf->Cell(100, 5, utf8_decode( $coordonnées['ref_clt3']));
        	$pdf->Ln(5);
        }
        $pdf->Ln(10);
        
        $pdf->MultiCell(180, 5, utf8_decode("Comme convenu, nous vous prions de trouver votre devis"));
        // Cadre
        $pdf->Ln(5);
        $pdf->SetFillColor(200, 200, 200);
        $pdf->Cell(30, 5, 'Type', 1, 0, 'L', 1);
        $pdf->Cell(80, 5, 'Description', 1, 0, 'L', 1);
        $pdf->Cell(20, 5, 'Quantite', 1, 0, 'L', 1);
        $pdf->Cell(30, 5, 'Prix', 1, 0, 'L', 1);
        $pdf->Cell(30, 5, 'Total', 1, 0, 'L', 1);
        
        $total = 0;
        foreach ($details as $detail) {
			$montant=$detail['quantite']*$detail['pu'];
        	
        	$pdf->Ln();
            $pdf->SetFillColor(255, 255, 255);

            $pdf->Cell(30, 5, $detail['ref'], 1);
            $pdf->Cell(80, 5, utf8_decode($detail['designation']) , 1);
            $pdf->Cell(20, 5, utf8_decode($detail['quantite']), 1,0,"C");
            $pdf->Cell(30, 5, utf8_decode($detail['pu']) . utf8_decode(' '), 1,0,"R");
            $pdf->Cell(30, 5, utf8_decode($montant) . utf8_decode(' '), 1,0,"R");
                       
            $total += $montant;
        }

        $pdf->Ln();
        $pdf->SetFillColor(225, 225, 225);
        $pdf->Cell(160, 5, 'TOTAL', 1, 0, 'L', 1);
        $pdf->Cell(30, 5, $total . utf8_decode(' '), 1, 0, 'R', 1);

        $pdf->Ln(15);
        $pdf->Cell(10, 5, 'TVA non applicable - art. 293B du CGI');
        $pdf->Ln(10);
        $pdf->Cell(10, 5, 'Observations : ');
        $pdf->Ln(5);
        $pdf->Cell(130, 5, $coordonnées['observation'], 0, 0, 'L', 0);
         
        if (is_null($chemin)) {
            $pdf->Output('devis.pdf', 'D');
        } else {
            $pdf->Output($chemin, 'F');
        }
    }

    /**
     * Envoi par mail d'une facture au format PDF
     *
     * @param   string     $reference      Reference de la facturation
     * @access public
     * @return bool Succès de l'envoi
     */
    function envoyerFacture($reference)
    {
        require_once 'Afup/AFUP_Configuration.php';
        $configuration = $GLOBALS['AFUP_CONF'];

        require_once 'phpmailer/class.phpmailer.php';
        $personne = $this->obtenir($reference, 'email, nom, prenom');

        $mail = new PHPMailer;
        $mail->AddAddress($personne['email'], $personne['nom']." ".$personne['prenom']);

        $mail->From     = $configuration->obtenir('mails|email_expediteur');
        $mail->FromName = $configuration->obtenir('mails|nom_expediteur');

        if ($configuration->obtenir('mails|serveur_smtp')) {
            $mail->Host     = $configuration->obtenir('mails|serveur_smtp');
            $mail->Mailer   = "smtp";
        } else {
            $mail->Mailer   = "mail";
        }

        $sujet  = "Facture AFUP\n";
        $mail->Subject = $sujet;

        $corps  = "Bonjour, \n\n";
        $corps .= "Veuillez trouver ci-joint la facture correspondant à la participation au forum organisé par l'AFUP.\n";
        $corps .= "Nous restons à votre disposition pour toute demande complémentaire.\n\n";
        $corps .= "Le bureau\n\n";
        $corps .= $configuration->obtenir('afup|raison_sociale')."\n";
        $corps .= $configuration->obtenir('afup|adresse')."\n";
        $corps .= $configuration->obtenir('afup|code_postal')." ".$configuration->obtenir('afup|ville')."\n";

        $mail->Body = $corps;

        $chemin_facture = AFUP_CHEMIN_RACINE . 'cache'. DIRECTORY_SEPARATOR .'fact' . $reference . '.pdf';
        $this->genererFacture($reference, $chemin_facture);
        $mail->AddAttachment($chemin_facture, 'facture.pdf');
        $ok = $mail->Send();
        @unlink($chemin_facture);

        $ok = true;
        if ($ok) {
            $this->estFacture($reference);
        }

        return $ok;
    }
    
   function envoyerDevis($reference)
    {
        require_once 'Afup/AFUP_Configuration.php';
        $configuration = $GLOBALS['AFUP_CONF'];

        require_once 'phpmailer/class.phpmailer.php';
        $personne = $this->obtenir($reference, 'email, nom, prenom');

        $mail = new PHPMailer;
        $mail->AddAddress($personne['email'], $personne['nom']." ".$personne['prenom']);

        $mail->From     = $configuration->obtenir('mails|email_expediteur');
        $mail->FromName = $configuration->obtenir('mails|nom_expediteur');

        if ($configuration->obtenir('mails|serveur_smtp')) {
            $mail->Host     = $configuration->obtenir('mails|serveur_smtp');
            $mail->Mailer   = "smtp";
        } else {
            $mail->Mailer   = "mail";
        }

        $sujet  = "Devis AFUP\n";
        $mail->Subject = $sujet;

        $corps  = "Bonjour, \n\n";
        $corps .= "Comme convenu, nous vous prions de trouver ci-joint.\n";
        $corps .= "Nous restons à votre disposition pour toute demande complémentaire.\n\n";
        $corps .= "Le bureau\n\n";
        $corps .= $configuration->obtenir('afup|raison_sociale')."\n";
        $corps .= $configuration->obtenir('afup|adresse')."\n";
        $corps .= $configuration->obtenir('afup|code_postal')." ".$configuration->obtenir('afup|ville')."\n";

        $mail->Body = $corps;

        $chemin_devis = AFUP_CHEMIN_RACINE . 'cache'. DIRECTORY_SEPARATOR .'devis' . $reference . '.pdf';
        $this->genererFacture($reference, $chemin_facture);
        $mail->AddAttachment($chemin_facture, 'devis.pdf');
        $ok = $mail->Send();
        @unlink($chemin_devis);

        $ok = true;
  //      if ($ok) {
 //           $this->estFacture($reference);
   //     }

        return $ok;
    }
    
}

?>
