<?php


class AFUP_Compta
{
    var $_bdd;
    
    function AFUP_Compta(&$bdd)
    {
        $this->_bdd = $bdd;   
    }  

    function periodeDebutFin ($debutFin='debut',$date)
    {
		if ($date != '')
		{ 
			return $date;
		}
		
    	if ($debutFin=='debut')
				return DATE("Y")."-01-01";
		else
				return DATE("Y")."-12-31";
		
    }

    /* affiche le journal de :
     * courant = Compte courant
     * Livret A
     * Espece
     * Paypal
     * 
     */
   function obtenirJournalBanque($compte='courant',
                          $periode_debut= '',
                          $periode_fin=''
                          )
    {
    	
     $periode_debut=$this->periodeDebutFin ($debutFin='debut',$periode_debut);
     $periode_fin=$this->periodeDebutFin ($debutFin='fin',$periode_fin);
     
if ($compte=="courant") $typeJournal=" AND idevenement!='18' AND idmode_regl!='1' AND idmode_regl!='7' AND idmode_regl!='8' ";
if ($compte=="livreta") $typeJournal=" AND idevenement='18' ";
if ($compte=="espece") $typeJournal=" AND idmode_regl='1' ";
if ($compte=="paypal") $typeJournal=" AND idmode_regl='8' ";
     

     
     
		$requete  = 'SELECT ';
		$requete .= 'compta.date_ecriture, compta.description, compta.montant, compta.idoperation, compta.date_regl, ';
		$requete .= 'compta_reglement.reglement ';
		$requete .= 'FROM  ';
		$requete .= 'compta,  ';
		$requete .= 'compta_reglement  ';
		$requete .= 'WHERE  ';
		$requete .= 'compta.date_regl >= \''.$periode_debut.'\' '; 
		$requete .= 'AND compta.date_regl <= \''.$periode_fin.'\'  ';
		$requete .= 'AND compta.montant != \'0.00\' ';
		$requete .= 'AND compta.idmode_regl = compta_reglement.id ';
		$requete .= $typeJournal;
		$requete .= 'ORDER BY ';
		$requete .= 'compta.date_regl ';

		return $this->_bdd->obtenirTous($requete);
    }

    /* Journal des opération
     * 
     */
   
	function obtenirJournal($ordre      = '',
                          $periode_debut= '',
                          $periode_fin=''
                          ) 
    {
    	
     $periode_debut=$this->periodeDebutFin ($debutFin='debut',$periode_debut);
     $periode_fin=$this->periodeDebutFin ($debutFin='fin',$periode_fin);
    	    

		$requete  = 'SELECT ';
		$requete .= 'compta.date_ecriture, compta.description, compta.montant, compta.idoperation,compta.id as idtmp, ';
		$requete .= 'compta_reglement.reglement, ';
		$requete .= 'compta_evenement.evenement, ';
		$requete .= 'compta_categorie.categorie   '; 
		$requete .= 'FROM  ';
		$requete .= 'compta,  ';
		$requete .= 'compta_categorie, ';  
		$requete .= 'compta_reglement, ';  
		$requete .= 'compta_evenement ';  
		$requete .= 'WHERE  ';
		$requete .= 'compta.date_ecriture >= \''.$periode_debut.'\' '; 
		$requete .= 'AND compta.date_ecriture <= \''.$periode_fin.'\'  ';
		$requete .= 'AND compta.idcategorie = compta_categorie.id ';
		$requete .= 'AND compta.idmode_regl = compta_reglement.id ';
		$requete .= 'AND compta.idevenement  = compta_evenement.id ';
		$requete .= 'ORDER BY ';
		$requete .= 'compta.date_ecriture ';
		
		return $this->_bdd->obtenirTous($requete);
    }
    
    // mise en forme du montant
    function formatMontantCompta($valeur)
    {
    	$prix_ok = number_format($valeur,2, ',', ' ');

		return $prix_ok;
    	
    }

	function obtenirFormesOperations()
	{
		$requete  = 'SELECT ';
		$requete .= 'id, operation ';
		$requete .= 'FROM  ';
		$requete .= 'compta_operation  ';
		$data=$this->_bdd->obtenirTous($requete);
		
		$formesOperations[-1] = "";
		foreach ($data as $row)
		{
			$formesOperations[$row['id']] = $row['operation'];
		}
		return $formesOperations;		
	}

	function obtenirFormesCategories()
	{
		$requete  = 'SELECT ';
		$requete .= 'id, idevenement, categorie ';
		$requete .= 'FROM  ';
		$requete .= 'compta_categorie  ';
		$data=$this->_bdd->obtenirTous($requete);
		
		foreach ($data as $row)
		{
			$formesCategories[$row['id']] = $row['categorie'];
		}
		return $formesCategories;		
	}
	function obtenirFormesEvenements()
	{
		$requete  = 'SELECT ';
		$requete .= 'id, evenement ';
		$requete .= 'FROM  ';
		$requete .= 'compta_evenement  ';
		$requete .= 'ORDER BY ';
		$requete .= 'evenement ';
		$data=$this->_bdd->obtenirTous($requete);
		
		foreach ($data as $row)
		{
			$formesEvenements[$row['id']] = $row['evenement'];
		}
		
		return $formesEvenements;		
	}
	
	function obtenirFormesReglements()
	{
		$requete  = 'SELECT ';
		$requete .= 'id, reglement ';
		$requete .= 'FROM  ';
		$requete .= 'compta_reglement  ';

		$data=$this->_bdd->obtenirTous($requete);
		
		$formesReglements[-1] = '--';
		foreach ($data as $row)
		{
			$formesReglements[$row['id']] = $row['reglement'];
		}
		return $formesReglements;		
	}

	function ajouter($idoperation,$idcategorie,$date_ecriture,$nom_frs,$montant,$description,
					$numero,$idmode_regl,$date_regl,$obs_regl,$idevenement)
	{
	
		$requete = 'INSERT INTO ';
		$requete .= 'compta (';
		$requete .= 'idoperation,idcategorie,date_ecriture,nom_frs,montant,description,';
		$requete .= 'numero,idmode_regl,date_regl,obs_regl,idevenement) ';
		$requete .= 'VALUES (';
		$requete .= $this->_bdd->echapper($idoperation) . ',';
		$requete .= $this->_bdd->echapper($idcategorie) . ',';
		$requete .= $this->_bdd->echapper($date_ecriture) . ',';
		$requete .= $this->_bdd->echapper($nom_frs) . ',';
		$requete .= $this->_bdd->echapper($montant) . ',';
		$requete .= $this->_bdd->echapper($description) . ',';
		$requete .= $this->_bdd->echapper($numero) . ',';
		$requete .= $this->_bdd->echapper($idmode_regl) . ',';
		$requete .= $this->_bdd->echapper($date_regl) . ',';
		$requete .= $this->_bdd->echapper($obs_regl) . ',';
		$requete .= $this->_bdd->echapper($idevenement) . ' ';
		$requete .= ');';
	
		return $this->_bdd->executer($requete);
	}

	function modifier($id,$idoperation,$idcategorie,$date_ecriture,$nom_frs,$montant,$description,
					$numero,$idmode_regl,$date_regl,$obs_regl,$idevenement)
	{
	
		$requete = 'UPDATE ';
		$requete .= 'compta ';
		$requete .= 'SET ';
		$requete .= 'idoperation='.$this->_bdd->echapper($idoperation) . ',';
		$requete .= 'idcategorie='.$this->_bdd->echapper($idcategorie) . ',';
		$requete .= 'date_ecriture='.$this->_bdd->echapper($date_ecriture) . ',';
		$requete .= 'nom_frs='.$this->_bdd->echapper($nom_frs) . ',';
		$requete .= 'montant='.$this->_bdd->echapper($montant) . ',';
		$requete .= 'description='.$this->_bdd->echapper($description) . ',';
		$requete .= 'numero='.$this->_bdd->echapper($numero) . ',';
		$requete .= 'idmode_regl='.$this->_bdd->echapper($idmode_regl) . ',';
		$requete .= 'date_regl='.$this->_bdd->echapper($date_regl) . ',';
		$requete .= 'obs_regl='.$this->_bdd->echapper($obs_regl) . ',';
		$requete .= 'idevenement='.$this->_bdd->echapper($idevenement) . ' ';
		$requete .= 'WHERE ';
		$requete .= 'id=' . $id;

		return $this->_bdd->executer($requete);
	}
	
    function obtenir($id) 
    {
        $requete  = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  compta ';
        $requete .= 'WHERE id=' . $id;
        
        return $this->_bdd->obtenirEnregistrement($requete);
    }

 
	function obtenirSyntheseEvenement($idoperation='1',$idevenement='') 
    {    
		$requete  = 'SELECT ';
		$requete .= 'compta.*, ';
		$requete .= 'compta_categorie.id, compta_categorie.categorie   '; 
		$requete .= 'FROM  ';
		$requete .= 'compta,  ';
		$requete .= 'compta_categorie, ';  
		$requete .= 'WHERE  ';
		$requete .= 'compta.idoperation = \''.$idoperation.'\' '; 
		$requete .= 'AND compta.idevenement = \''.$idevenement.'\'  ';
		$requete .= 'AND compta.idcategorie = compta_categorie.id ';
		$requete .= 'ORDER BY ';
		$requete .= 'compta_categorie.categorie, ';
		$requete .= 'compta.date_ecriture ';
echo $requete;		
		return $this->_bdd->obtenirTous($requete);
    }
 
    function obtenirBilan($idoperation='1',$periode_debut='',$periode_fin='')
    {
     $periode_debut=$this->periodeDebutFin ($debutFin='debut',$periode_debut);
     $periode_fin=$this->periodeDebutFin ($debutFin='fin',$periode_fin);

     	$requete  = 'SELECT ';
		$requete .= 'compta.*, ';
		$requete .= 'compta_categorie.id, compta_categorie.categorie   '; 
		$requete .= 'FROM  ';
		$requete .= 'compta,  ';
		$requete .= 'compta_categorie, ';  
		$requete .= 'WHERE  ';
		$requete .= 'compta.idoperation = \''.$idoperation.'\' '; 
		$requete .= 'AND compta.date_regl >= \''.$periode_debut.'\' '; 
		$requete .= 'AND compta.date_regl <= \''.$periode_fin.'\'  ';
		//	$requete .= 'AND compta.idevenement = \''.$idevenement.'\'  ';
		$requete .= 'AND compta.idcategorie = compta_categorie.id ';
		$requete .= 'ORDER BY ';
		$requete .= 'compta.date_ecriture, ';
		$requete .= 'compta_categorie.categorie ';
		
$sql="SELECT compta.*,compta_categorie.id,compta_categorie.categorie   
	FROM compta, compta_categorie  
	WHERE compta.idoperation='1' AND compta.idevenement=$idevenement 
	         AND compta.date_ecriture>='$periode_debut' AND compta.date_ecriture<='$periode_fin' 
		  AND compta.idcategorie = compta_categorie.id     
	ORDER BY compta.date_ecriture,compta.idevenement,compta.idcategorie";    	
echo $requete;		
		return $this->_bdd->obtenirTous($requete);
    }
}

?>