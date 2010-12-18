<?php
//@TODO
// Ajout période comptable automatiquement

class AFUP_Compta
{
    var $_bdd;
    
    function AFUP_Compta(&$bdd)
    {
        $this->_bdd = $bdd;   
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
     
if ($compte=="courant") $typeJournal="  AND idevenement!='18' 
					AND idmode_regl!='1' 
					AND idmode_regl!='7' 
					AND idmode_regl!='8' 
				    ";
if ($compte=="livreta") $typeJournal=" AND idevenement='18' ";
if ($compte=="espece") $typeJournal=" AND idmode_regl='1' ";
if ($compte=="paypal") $typeJournal=" AND idmode_regl='8' ";
     

     
     
		$requete  = 'SELECT ';
		$requete .= 'compta.date_ecriture, compta.description, compta.montant, compta.idoperation, compta.date_regl, compta.id as idtmp, ';
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

    function obtenirTotalJournalBanque($idoperation='1',$compte='courant',$periode_debut,$periode_fin) 
    {    
    	
    $data=$this->obtenirJournalBanque($compte,$periode_debut,$periode_fin);	
     
		$total=0;
		foreach ($data as $id=>$row)
		{
			
			if ($idoperation==$row['idoperation'])
			$total += $row['montant'];
		}

		return $total;
    }
    
    /* Journal des opération
     * 
     */
   
	function obtenirJournal($debitCredit = '',
                          $periode_debut= '',
                          $periode_fin=''
                          ) 
    {
    	
     $periode_debut=$this->periodeDebutFin ($debutFin='debut',$periode_debut);
     $periode_fin=$this->periodeDebutFin ($debutFin='fin',$periode_fin);
    	    
		if ($debitCredit == 1 || $debitCredit == 2)			
			$filtre = 'AND compta.idoperation =\''.$debitCredit.'\'  '; 
		else 
			$filtre="";
		
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
		$requete .= ' compta.date_ecriture >= \''.$periode_debut.'\' '; 
		$requete .= 'AND compta.date_ecriture <= \''.$periode_fin.'\'  ';
		$requete .= 'AND compta.idcategorie = compta_categorie.id ';
		$requete .= 'AND compta.idmode_regl = compta_reglement.id ';
		$requete .= 'AND compta.idevenement  = compta_evenement.id ';
		$requete .= $filtre;
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

    function periodeDebutFin ($debutFin='debut',$date='')
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
    
    function obtenirPeriodeEnCours($id_periode)
	{
		// Si la periode existe
		if ($id_periode != "")
		{
			return $id_periode;
		}

		// Sinon definir la periode en cours
		$date_debut=$this->periodeDebutFin ('debut');
		$date_fin=$this->periodeDebutFin ('fin');
		$result=$this->obtenirListPeriode($date_debut,$date_fin);

		return $result[0]['id'];		

		// prevoir ajout période automatique
		
	}
    
    function obtenirListPeriode($date_debut='',$date_fin='')
	{
		$requete  = 'SELECT ';
		$requete .= 'id, date_debut,date_fin, verouiller ';
		$requete .= 'FROM  ';
		$requete .= 'compta_periode  ';

		if ($date_debut != '' AND $date_fin != '')
		{
			$requete .= 'WHERE ';		
			$requete .= 'compta_periode.date_debut= \''.$date_debut.'\'  ';
			$requete .= 'AND compta_periode.date_fin= \''.$date_fin.'\'  ';			
		}

		return $this->_bdd->obtenirTous($requete);
	}
	
	function obtenirListOperations()
	{
		$requete  = 'SELECT ';
		$requete .= 'id, operation ';
		$requete .= 'FROM  ';
		$requete .= 'compta_operation  ';

	/*	if ($filtre)	{
			return $this->_bdd->obtenirTous($requete);					
		} else {*/
			$data=$this->_bdd->obtenirTous($requete);		
			$result[]="";
			foreach ($data as $row)
			{
				$result[$row['id']]=$row['operation'];
			}
			
			return $result;
//		}
    


	}

	function obtenirListCategories()
	{
		$requete  = 'SELECT ';
		$requete .= 'id, idevenement, categorie ';
		$requete .= 'FROM  ';
		$requete .= 'compta_categorie  ';

	/*	if ($filtre)	{
			return $this->_bdd->obtenirTous($requete);					
		} else {	*/	
			$data=$this->_bdd->obtenirTous($requete);		
			$result[]="";
			foreach ($data as $row)
			{
				$result[$row['id']]=$row['categorie'];
			}
			
			return $result;
	//	}
		
	}

	function obtenirListEvenements($filtre='')
	{
		$requete  = 'SELECT ';
		$requete .= 'id, evenement ';
		$requete .= 'FROM  ';
		$requete .= 'compta_evenement  ';
		$requete .= 'ORDER BY ';
		$requete .= 'evenement ';

		if ($filtre)	{
			return $this->_bdd->obtenirTous($requete);					
		} else {		
			$data=$this->_bdd->obtenirTous($requete);		
			$result[]="";
			foreach ($data as $row)
			{
				$result[$row['id']]=$row['evenement'];
			}
			
			return $result;
		}
	}
	
	function obtenirListReglements()
	{
		$requete  = 'SELECT ';
		$requete .= 'id, reglement ';
		$requete .= 'FROM  ';
		$requete .= 'compta_reglement  ';

	/*	if ($filtre)	{
			return $this->_bdd->obtenirTous($requete);					
		} else {		*/
			$data=$this->_bdd->obtenirTous($requete);		
			$result[]="";
			foreach ($data as $row)
			{
				$result[$row['id']]=$row['reglement'];
			}
			
			return $result;				
	//*	}
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
echo $requete;
exit;
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

 
	function obtenirSyntheseEvenement($idoperation='1',$idevenement) 
    {    
		$requete  = 'SELECT ';
		$requete .= 'compta.*, ';
		$requete .= 'compta_categorie.id, compta_categorie.categorie   '; 
		$requete .= 'FROM  ';
		$requete .= 'compta,  ';
		$requete .= 'compta_categorie ';  
		$requete .= 'WHERE  ';
		$requete .= 'compta.idevenement = \''.$idevenement.'\' '; 
		$requete .= 'AND compta.idoperation = \''.$idoperation.'\' '; 
		$requete .= 'AND compta.idcategorie = compta_categorie.id ';
		$requete .= 'ORDER BY ';
		$requete .= 'compta_categorie.categorie, ';
		$requete .= 'compta.date_ecriture ';

		return $this->_bdd->obtenirTous($requete);

    }

    
	function obtenirTotalSyntheseEvenement($idoperation='1',$idevenement) 
    {    
		$requete  = 'SELECT ';
		$requete .= 'compta.montant ';
		$requete .= 'FROM  ';
		$requete .= 'compta  ';
		$requete .= 'WHERE  ';
		$requete .= 'compta.idevenement = \''.$idevenement.'\' '; 
		$requete .= 'AND compta.idoperation = \''.$idoperation.'\' '; 

		$data = $this->_bdd->obtenirTous($requete);
	
		$total=0;
		foreach ($data as $id=>$row)
		{
/*			echo "<pre>";
			print_r($row);
			echo "</pre>";*/
//echo $row['montant']."<br>";
			$total += $row['montant'];
		}
//§		print_r($total);
		return $total;
    }
    
    function obtenirBilan($idoperation='1',$periode_debut='',$periode_fin='')
    {
     $periode_debut=$this->periodeDebutFin ($debutFin='debut',$periode_debut);
     $periode_fin=$this->periodeDebutFin ($debutFin='fin',$periode_fin);

     	$requete  = 'SELECT ';
		$requete .= 'compta.montant, ';
		$requete .= 'compta_categorie.id, compta_categorie.categorie   '; 
		$requete .= 'FROM  ';
		$requete .= 'compta,  ';
		$requete .= 'compta_categorie ';  
		$requete .= 'WHERE  ';
		$requete .= 'compta.idoperation = \''.$idoperation.'\' '; 
		$requete .= 'AND compta.date_regl >= \''.$periode_debut.'\' '; 
		$requete .= 'AND compta.date_regl <= \''.$periode_fin.'\'  ';
		//	$requete .= 'AND compta.idevenement = \''.$idevenement.'\'  ';
		$requete .= 'AND compta.idcategorie = compta_categorie.id ';
		$requete .= 'ORDER BY ';
		$requete .= 'compta.date_ecriture, ';
		$requete .= 'compta.idcategorie ';
	/*	
$sql="SELECT compta.*,compta_categorie.id,compta_categorie.categorie   
	FROM compta, compta_categorie  
	WHERE compta.idoperation='1' AND compta.idevenement=$idevenement 
	         AND compta.date_ecriture>='$periode_debut' AND compta.date_ecriture<='$periode_fin' 
		  AND compta.idcategorie = compta_categorie.id     
	ORDER BY compta.date_ecriture,compta.idevenement,compta.idcategorie";    	
*/
echo $requete;		
		return $this->_bdd->obtenirTous($requete);
    }


   function obtenirBalance($periode_debut='',$periode_fin='')
   {
     $periode_debut=$this->periodeDebutFin ($debutFin='debut',$periode_debut);
     $periode_fin=$this->periodeDebutFin ($debutFin='fin',$periode_fin);
   	
// $this->obtenirBalanceDetails('1','11',$periode_debut='',$periode_fin='');
// exit;    

     	$requete  = 'SELECT ';
     	$requete .= ' SUM( IF( compta.idoperation =1, compta.montant, "" ) ) AS debit, ';
     	$requete .= ' SUM( IF( compta.idoperation =2, compta.montant, "" ) ) AS credit, ';
     	$requete .= ' compta.date_ecriture,compta.montant,compta.idoperation, compta.idevenement, ';
		$requete .= ' compta_evenement.id,compta_evenement.evenement ';
		$requete .= 'FROM  ';
		$requete .= ' compta,  ';
		$requete .= ' compta_evenement ';  
		$requete .= 'WHERE  ';
		$requete .= ' compta.idevenement = compta_evenement.id ';
		$requete .= ' AND compta.date_ecriture >= \''.$periode_debut.'\' '; 
		$requete .= ' AND compta.date_ecriture <= \''.$periode_fin.'\'  ';
		$requete .= 'GROUP BY ';
		$requete .= ' compta_evenement.evenement ';
		$requete .= 'ORDER BY  ';
		$requete .= ' compta_evenement.evenement ';

		return $this->_bdd->obtenirTous($requete);
   }
    
    function obtenirTotalBalance($idoperation='1',$periode_debut,$periode_fin) 
    {    
    	
	    $data=$this->obtenirBalance($periode_debut,$periode_fin);	
     
		$total=0;
		foreach ($data as $id=>$row)
		{
			
			if ($idoperation==$row['idoperation'])
				$total += $row['montant'];
		}

		return $total;
    }
   
   function obtenirBalanceDetails($evenement,$periode_debut='',$periode_fin='')
   {
     $periode_debut=$this->periodeDebutFin ($debutFin='debut',$periode_debut);
     $periode_fin=$this->periodeDebutFin ($debutFin='fin',$periode_fin);
   	

     	$requete  = 'SELECT ';
     	$requete .= ' IF( compta.idoperation =1, compta.montant, "" )  AS debit, ';
     	$requete .= ' IF( compta.idoperation =2, compta.montant, "" )  AS credit, ';
     	$requete .= 'compta.description, ';
     	$requete .= ' compta.date_ecriture,compta.montant,compta.idoperation, compta.idevenement, ';
		$requete .= ' compta_categorie.id,compta_categorie.categorie ';
		$requete .= 'FROM  ';
		$requete .= ' compta,  ';
		$requete .= ' compta_categorie ';  
		$requete .= 'WHERE  ';
		$requete .= ' compta.idcategorie = compta_categorie.id ';
		$requete .= ' AND compta.date_ecriture >= \''.$periode_debut.'\' '; 
		$requete .= ' AND compta.date_ecriture <= \''.$periode_fin.'\'  ';
		$requete .= ' AND compta.idevenement = \''.$evenement.'\' ';
		$requete .= 'ORDER BY  ';
		$requete .= ' compta_categorie.categorie ';

		return $this->_bdd->obtenirTous($requete);
      } 
   
}

?>
