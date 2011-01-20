<?php
//@TODO
// Ajout période comptable automatiquement
// revoir sous totaux balance
// test champ obligatoire lors de la saisie

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
		$requete .= 'compta.date_regl, compta.description, compta.montant, compta.idoperation,  ';
		$requete .= 'MONTH(compta.date_regl) as mois, compta.id as idtmp, ';
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

   
    function obtenirSousTotalJournalBanque($compte='courant',$periode_debut,$periode_fin) 
    {    
    	
    $data=$this->obtenirJournalBanque($compte,$periode_debut,$periode_fin);	

for ($i=1;$i<=12;$i++)
{
	$credit[$i]='';
	$debit[$i]='';
	$nligne[$i]='';
}
    	foreach ($data as $id=>$row)
		{
			if ($row['idoperation']=="1")				$debit[$row['mois']] += $row['montant'];
			if ($row['idoperation']=="2")				$credit[$row['mois']] += $row['montant'];
			if ($row['idoperation']=="1" || $row['idoperation']=="2") $nligne[$row['mois']]++;
		}

$dif_old=0;
for ($i=1;$i<=12;$i++)
{
	$dif=$dif_old+$credit[$i]-$debit[$i];
	$tableau[$i] = array("mois"=>$i,
						"debit"=>$debit[$i],
						"credit"=>$credit[$i],
						"dif"=>$dif,
						"nligne"=>$nligne[$i]
						);
	$dif_old=$dif;
}

		return $tableau;
    }
    
    function obtenirTotalJournalBanque($compte='courant',$periode_debut,$periode_fin) 
    {    
    	
    $data=$this->obtenirJournalBanque($compte,$periode_debut,$periode_fin);	
 /* echo "<pre>";
print_r($data);
echo "</pre>";*/
	$credit=0;
	$debit=0;

    	foreach ($data as $id=>$row)
		{
			if ($row['idoperation']=="1")				$debit += $row['montant'];
			if ($row['idoperation']=="2")				$credit += $row['montant'];
		}
//print_r($credit);
//$dif_old=0;
//for ($i=1;$i<=12;$i++)
//{
//	$dif=$dif_old+$credit[$i]-$debit[$i];
	$tableau = array(
						"debit"=>$debit,
						"credit"=>$credit,
						"dif"=>$credit-$debit
						);
//	$dif_old=$dif;
//}

		return $tableau;
/*		$total=0;
		foreach ($data as $id=>$row)
		{
			
			if ($idoperation==$row['idoperation'])
			$total += $row['montant'];
		}

		return $total;
		*/
		
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
 // echo "=>$debutFin*$date*<br>";  	
		if ($date != '')
		{ 
			return $date;
		}

		
    	if ($debutFin=='debut')
    	{
/*			if ($id_periode !='')
			{
				 $r=obtenirPeriodeEnCours($id_periode);
			} else {*/ 
    			return DATE("Y")."-01-01";
    //		}
    	}
		else
		{
		/*	if ($id_periode !='')
			{
				 $r=obtenirPeriodeEnCours($id_periode);
				 print_r($r);
				 return $r;
			} else {*/ 
    			return DATE("Y")."-12-31";
    		//}
		}
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

		if ($result)
		{
			return $result[0]['id'];			
		} 
		else				// ajout d'une nouvelle periode 
		{
			$result=$this->ajouterListPeriode();
			return $result[0]['id'];			
		}
	}

	function ajouterListPeriode()
	{
	
		$date_debut=DATE ("Y").'-01-01';
		$date_fin=DATE ("Y").'-12-31';
		
		$requete = 'INSERT INTO ';
		$requete .= 'compta_periode (';
		$requete .= 'date_debut,date_fin,verouiller) ';
		$requete .= 'VALUES (';
		$requete .= $this->_bdd->echapper($date_debut) . ',';
		$requete .= $this->_bdd->echapper($date_fin) . ',';
		$requete .= $this->_bdd->echapper(0) . ' ';
		$requete .= ');';

		$this->_bdd->executer($requete);
		return $this->obtenirListPeriode($date_debut,$date_fin);
		
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
	
	function obtenirListOperations($filtre='',$where='')
	{
		$requete  = 'SELECT ';
		$requete .= 'id, operation ';
		$requete .= 'FROM  ';
		$requete .= 'compta_operation  ';
        if ($where)		$requete .= 'WHERE id=' . $where. ' ';		
		
        $requete .= 'ORDER BY ';
		$requete .= 'operation ';

		if ($where) {
		        return $this->_bdd->obtenirEnregistrement($requete);
		}elseif ($filtre)	{
			return $this->_bdd->obtenirTous($requete);					
		} else {
			$data=$this->_bdd->obtenirTous($requete);		
			$result[]="";
			foreach ($data as $row)
			{
				$result[$row['id']]=$row['operation'];
			}
			
			return $result;
		}
	}

	function obtenirListCategories($filtre='',$where='')
	{
		$requete  = 'SELECT ';
		$requete .= 'id, idevenement, categorie ';
		$requete .= 'FROM  ';
		$requete .= 'compta_categorie  ';
        if ($where)		$requete .= 'WHERE id=' . $where. ' ';		
		
        $requete .= 'ORDER BY ';
		$requete .= 'categorie ';

		if ($where) {
		        return $this->_bdd->obtenirEnregistrement($requete);
		}elseif ($filtre)	{
			return $this->_bdd->obtenirTous($requete);					
		} else {		
			$data=$this->_bdd->obtenirTous($requete);		
			$result[]="";
			foreach ($data as $row)
			{
				$result[$row['id']]=$row['categorie'];
			}
			
			return $result;
		}
		
	}

	function obtenirListEvenements($filtre='',$where='')
	{
		$requete  = 'SELECT ';
		$requete .= 'id, evenement ';
		$requete .= 'FROM  ';
		$requete .= 'compta_evenement  ';
        if ($where)		$requete .= 'WHERE id=' . $where. ' ';		

        $requete .= 'ORDER BY ';
		$requete .= 'evenement ';

		if ($where) {
		        return $this->_bdd->obtenirEnregistrement($requete);
		}elseif ($filtre)	{
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
	
	function obtenirListReglements($filtre='',$where='')
	{
		$requete  = 'SELECT ';
		$requete .= 'id, reglement ';
		$requete .= 'FROM  ';
		$requete .= 'compta_reglement  ';
        if ($where)		$requete .= 'WHERE id=' . $where. ' ';		

        $requete .= 'ORDER BY ';
		$requete .= 'reglement ';

		if ($where) {
		        return $this->_bdd->obtenirEnregistrement($requete);
		}elseif ($filtre)	{
			return $this->_bdd->obtenirTous($requete);					
		} else {		
			$data=$this->_bdd->obtenirTous($requete);		
			$result[]="";
			foreach ($data as $row)
			{
				$result[$row['id']]=$row['reglement'];
			}
			
			return $result;				
		}
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
		$requete .= 'id=' . $id. ' ';

		return $this->_bdd->executer($requete);
	}

	function ajouterConfig($table,$champ,$valeur)
	{
		$requete = 'INSERT INTO ';
		$requete .= ''.$table.' (';
		$requete .= ''.$champ.') ';
		$requete .= 'VALUES (';
		$requete .= $this->_bdd->echapper($valeur) . ' ';
		$requete .= ');';

		return $this->_bdd->executer($requete);
	}
	
	function modifierConfig($table,$id,$champ,$valeur)
	{
	
		$requete = 'UPDATE ';
		$requete .= ''.$table.' ';
		$requete .= 'SET ';
		$requete .= ''.$champ.' = '.$this->_bdd->echapper($valeur) . ' ';
		$requete .= 'WHERE ';
		$requete .= 'id = ' . $id. ' ';

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
			$total += $row['montant'];
		}
		return $total;
    }
    
    function obtenirBilan($idoperation='1',$periode_debut='',$periode_fin='')
    {
     $periode_debut=$this->periodeDebutFin ($debutFin='debut',$periode_debut);
     $periode_fin=$this->periodeDebutFin ($debutFin='fin',$periode_fin);

     	$requete  = 'SELECT ';
		$requete .= ' SUM(compta.montant) as montant, ';
		$requete .= ' compta_evenement.id, compta_evenement.evenement   '; 
		$requete .= 'FROM  ';
		$requete .= ' compta,  ';
		$requete .= ' compta_evenement ';  
		$requete .= 'WHERE  ';
		$requete .= ' compta.idoperation = \''.$idoperation.'\' '; 
		$requete .= ' AND compta.date_ecriture >= \''.$periode_debut.'\' '; 
		$requete .= ' AND compta.date_ecriture <= \''.$periode_fin.'\'  ';
		$requete .= ' AND compta.idevenement = compta_evenement.id ';
		$requete .= 'GROUP BY';
		$requete .= ' compta_evenement.evenement ';
		$requete .= 'ORDER BY ';
		$requete .= ' compta_evenement.evenement ';

		return $this->_bdd->obtenirTous($requete);
    }

    function obtenirTotalBilan($idoperation='1',$periode_debut,$periode_fin) 
    {    
    	
	    $data=$this->obtenirBilan($idoperation,$periode_debut,$periode_fin);	

		$total=0;
		foreach ($data as $id=>$row)
		{

			$total += $row['montant'];
		}

		return $total;
    }
 
   function obtenirBilanDetails($idoperation,$periode_debut='',$periode_fin='',$idevenement)
   {
    $periode_debut=$this->periodeDebutFin ($debutFin='debut',$periode_debut);
     $periode_fin=$this->periodeDebutFin ($debutFin='fin',$periode_fin);

     	$requete  = 'SELECT ';
     	$requete .= ' IF( compta.idoperation =1, compta.montant, "" )  AS debit, ';
     	$requete .= ' IF( compta.idoperation =2, compta.montant, "" )  AS credit, ';
     	$requete .= ' compta.date_ecriture, compta.description, ';
     	$requete .= ' montant, ';
		$requete .= ' compta_evenement.id, compta_evenement.evenement   '; 
		$requete .= 'FROM  ';
		$requete .= ' compta,  ';
		$requete .= ' compta_evenement ';  
		$requete .= 'WHERE  ';
		$requete .= ' compta.idoperation = \''.$idoperation.'\' '; 
		$requete .= ' AND compta.date_ecriture >= \''.$periode_debut.'\' '; 
		$requete .= ' AND compta.date_ecriture <= \''.$periode_fin.'\'  ';
		$requete .= ' AND compta.idevenement = compta_evenement.id ';
		$requete .= ' AND compta.idevenement = \''.$idevenement.'\' ';
		//$requete .= 'GROUP BY';
		//$requete .= ' compta_evenement.evenement ';
		$requete .= 'ORDER BY ';
		$requete .= ' compta.date_ecriture ';
//echo $requete."<br>";
		return $this->_bdd->obtenirTous($requete);
   	
   } 

   function obtenirSousTotalBilan($idoperation='1',$periode_debut,$periode_fin,$idevenement) 
    {    
    	
	    $data=$this->obtenirBilanDetails($idoperation,$periode_debut,$periode_fin,$idevenement);	

		$total=0;
		foreach ($data as $id=>$row)
		{

			$total += $row['montant'];
		}

		return $total;
    }
   
    
   function obtenirBalance($idoperation='',$periode_debut='',$periode_fin='')
   {
     $periode_debut=$this->periodeDebutFin ($debutFin='debut',$periode_debut);
     $periode_fin=$this->periodeDebutFin ($debutFin='fin',$periode_fin);
   	
     	$requete  = 'SELECT ';
     	$requete .= ' SUM( IF( compta.idoperation = 1, compta.montant, "" ) ) AS debit, ';
     	$requete .= ' SUM( IF( compta.idoperation = 2, compta.montant, "" ) ) AS credit, ';
     	$requete .= ' compta.date_ecriture,compta.montant,compta.idoperation, compta.idevenement, compta.id as idtmp, ';
		$requete .= ' compta_evenement.id,compta_evenement.evenement ';
		$requete .= 'FROM  ';
		$requete .= ' compta,  ';
		$requete .= ' compta_evenement ';  
		$requete .= 'WHERE  ';
		$requete .= ' compta.idevenement = compta_evenement.id ';
		$requete .= ' AND compta.date_ecriture >= \''.$periode_debut.'\' '; 
		$requete .= ' AND compta.date_ecriture <= \''.$periode_fin.'\'  ';
		if ($idoperation !='')
			$requete .= ' AND compta.idoperation = \''.$idoperation.'\' ';
		
		$requete .= 'GROUP BY ';
		$requete .= ' compta_evenement.evenement ';
		$requete .= 'ORDER BY  ';
		$requete .= ' compta_evenement.evenement ';

		return $this->_bdd->obtenirTous($requete);
   }
    
    function obtenirTotalBalance($idoperation='1',$periode_debut,$periode_fin) 
    {    
    	
	    $data=$this->obtenirBalance($idoperation,$periode_debut,$periode_fin);	

		$total=0;
		foreach ($data as $id=>$row)
		{
			if ($idoperation==1)			$total += $row['debit'];
			if ($idoperation==2)			$total += $row['credit'];
			
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
     	$requete .= 'compta.description, compta.id as idtmp, ';
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

     function obtenirSousTotalBalance($evenement,$periode_debut,$periode_fin) 
    {    
//	    	echo $evenement."*".$periode_debut."*".$periode_fin;
	    $data=$this->obtenirBalanceDetails($evenement,$periode_debut,$periode_fin);	

for ($i=1;$i<=30;$i++)
{
	$credit[$i]='';
	$debit[$i]='';
	$nligne[$i]=0;

}
		foreach ($data as $id=>$row)
		{
			if ($row['idoperation']=="1")		$debit[$row['id']] += $row['montant'];
			if ($row['idoperation']=="2")		$credit[$row['id']] += $row['montant'];
			if ($row['idoperation']=="1" || $row['idoperation']=="2") $nligne[$row['id']]++;
			
		}



for ($i=1;$i<=30;$i++)
{
	if ($debit[$i] || $credit[$i])
	{
		$tableau[$i] = array("idevenement"=>$i,
							"debit"=>$debit[$i],
							"credit"=>$credit[$i],
							"nligne"=>$nligne[$i]
						);
	}		
}		

		return $tableau;
    }

    
    function genererBilanPDF($periode_debut,$periode_fin)
    {

       // Construction du PDF
        require_once 'Afup/AFUP_Compta_PDF.php';
$pdf=new AFUP_Compta_PDF('L','mm','A4');
        //       $pdf = new AFUP_PDF_Compta();
 //       $pdf->AddPage();
$pdf->AliasNbPages();


$pdf->AddPage();

$pdf->SetFont('Times','B',18);
$pdf->Cell(0,5,"Bilan ",0,0,'C');
        

$debit=$this->obtenirBilan(1,$periode_debut='',$periode_fin='');    	
$credit=$this->obtenirBilan(2,$periode_debut='',$periode_fin='');    	

    	     
$header[]= array ("Categorie","Description","Montant");

$depense=0;
//while( $row=$qid->fetch(PDO::FETCH_OBJ) ) 
foreach ($debit as $debits)
{

$data[]= array ($debits->evenement ,
				$debits->description,
				$debits->montant
				);
$depense+=$debits->montant;
}

$data[]=array('','Total Dépenses',$depense);	

$pdf->Ln(10);  
//$pdf->$this->tableau(1,$header,$data);
    	
    
        if (is_null($chemin)) {
            $pdf->Output('bilan.pdf', 'D');
        } else {
            $pdf->Output($chemin, 'F');
        }    	
    	
    }
    
    


}

?>
