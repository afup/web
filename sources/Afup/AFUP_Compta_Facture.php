<?php
//@TODO
// Ajout période comptable automatiquement
// revoir sous totaux balance
// test champ obligatoire lors de la saisie

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
   
	function obtenirFacture() 
    {

		$requete  = 'SELECT ';
		$requete .= ' compta_facture.* '; 
		$requete .= 'FROM  ';
		$requete .= 'compta_facture  ';
//		$requete .= 'WHERE  ';
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

    function ajouter($date_ecriture,$societe,$service,$adresse,$code_postal,$ville,$id_pays,
					$email,$observation,$ref_clt1,$ref_clt2,$ref_clt3)
	{
	
		$requete = 'INSERT INTO ';
		$requete .= 'compta_facture (';
		$requete .= 'date_ecriture,societe,service,adresse,code_postal,ville,id_pays,';
		$requete .= 'email,observation,ref_clt1,ref_clt2,ref_clt3) ';
		$requete .= 'VALUES (';
		$requete .= $this->_bdd->echapper($date_ecriture) . ',';
		$requete .= $this->_bdd->echapper($societe) . ',';
		$requete .= $this->_bdd->echapper($service) . ',';
		$requete .= $this->_bdd->echapper($adresse) . ',';
		$requete .= $this->_bdd->echapper($code_postal) . ',';
		$requete .= $this->_bdd->echapper($ville) . ',';
		$requete .= $this->_bdd->echapper($id_pays) . ',';
		$requete .= $this->_bdd->echapper($email) . ',';
		$requete .= $this->_bdd->echapper($observation) . ',';
		$requete .= $this->_bdd->echapper($ref_clt1) . ',';
		$requete .= $this->_bdd->echapper($ref_clt2) . ',';
		$requete .= $this->_bdd->echapper($ref_clt3) . ' ';
		$requete .= ');';

		return $this->_bdd->executer($requete);
	}

	function modifier($id,$date_ecriture,$societe,$service,$adresse,$code_postal,$ville,$id_pays,
					$email,$observation,$ref_clt1,$ref_clt2,$ref_clt3)
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
		$requete .= 'email='.$this->_bdd->echapper($email) . ',';
		$requete .= 'observation='.$this->_bdd->echapper($observation) . ', ';
		$requete .= 'ref_clt1='.$this->_bdd->echapper($ref_clt1) . ',';
		$requete .= 'ref_clt2='.$this->_bdd->echapper($ref_clt2) . ',';
		$requete .= 'ref_clt3='.$this->_bdd->echapper($ref_clt3) . ' ';
		$requete .= 'WHERE ';
		$requete .= 'id=' . $id. ' ';

		return $this->_bdd->executer($requete);
	}
    

}

?>
