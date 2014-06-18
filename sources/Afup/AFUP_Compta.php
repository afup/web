<?php
//@TODO
// Ajout période comptable automatiquement
// revoir sous totaux balance
// test champ obligatoire lors de la saisie
// ajout filtre par mois pour les journaux banques
require_once dirname(__FILE__) . '/AFUP_Forum.php';

class AFUP_Compta
{
    var $_bdd;
    public $lastId = null;

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
   function obtenirJournalBanque($compte=1,
                          $periode_debut= '',
                          $periode_fin=''
                          )
    {

     $periode_debut=$this->periodeDebutFin ($debutFin='debut',$periode_debut);
     $periode_fin=$this->periodeDebutFin ($debutFin='fin',$periode_fin);
		$requete  = 'SELECT ';
		$requete .= 'compta.date_regl, compta.description, compta.montant, compta.idoperation,  ';
		$requete .= 'MONTH(compta.date_regl) as mois, compta.id as idtmp, ';
        $requete .= 'compta_reglement.reglement, ';
        $requete .= 'compta_evenement.evenement, compta.idevenement, ';
        $requete .= 'compta_categorie.categorie, compta.idcategorie ';
		$requete .= 'FROM  ';
		$requete .= 'compta  ';
        $requete .= 'LEFT JOIN ';
        $requete .= 'compta_categorie on compta_categorie.id=compta.idcategorie ';
        $requete .= 'LEFT JOIN ';
        $requete .= 'compta_reglement on compta_reglement.id=compta.idmode_regl ';
        $requete .= 'LEFT JOIN ';
        $requete .= 'compta_evenement on compta_evenement.id=compta.idevenement ';
		$requete .= 'WHERE  ';
		$requete .= 'compta.date_regl >= \''.$periode_debut.'\' ';
		$requete .= 'AND compta.date_regl <= \''.$periode_fin.'\'  ';
		$requete .= 'AND compta.montant != \'0.00\' ';
		$requete .= 'AND compta.idmode_regl = compta_reglement.id ';
		$requete .= 'AND idcompte = '.(int) $compte. ' ';
		$requete .= 'ORDER BY ';
		$requete .= 'compta.date_regl ';
		return $this->_bdd->obtenirTous($requete);
    }


    function obtenirSousTotalJournalBanque($compte=1,$periode_debut,$periode_fin)
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

    function obtenirTotalJournalBanque($compte=1,$periode_debut,$periode_fin)
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
		$requete .= 'compta_categorie.categorie, ';
		$requete .= 'compta_compte.nom_compte    ';
		$requete .= 'FROM ';
		$requete .= 'compta ';
		$requete .= 'LEFT JOIN ';
		$requete .= 'compta_categorie on compta_categorie.id=compta.idcategorie ';
		$requete .= 'LEFT JOIN ';
		$requete .= 'compta_reglement on compta_reglement.id=compta.idmode_regl ';
		$requete .= 'LEFT JOIN ';
		$requete .= 'compta_evenement on compta_evenement.id=compta.idevenement ';
		$requete .= 'LEFT JOIN ';
		$requete .= 'compta_compte on compta_compte.id=compta.idcompte ';
		$requete .= 'WHERE ';
		$requete .= ' compta.date_ecriture >= \''.$periode_debut.'\' ';
		$requete .= 'AND compta.date_ecriture <= \''.$periode_fin.'\'  ';
		$requete .= $filtre;
		$requete .= 'ORDER BY ';
		$requete .= 'compta.date_ecriture, numero_operation';

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

	function obtenirListComptes($filtre='',$where='')
	{
		$requete  = 'SELECT ';
		$requete .= 'id, nom_compte ';
		$requete .= 'FROM  ';
		$requete .= 'compta_compte  ';
        if ($where)		$requete .= 'WHERE id=' . $where. ' ';

        $requete .= 'ORDER BY ';
		$requete .= 'nom_compte ';

		if ($where) {
		        return $this->_bdd->obtenirEnregistrement($requete);
		}elseif ($filtre)	{
			return $this->_bdd->obtenirTous($requete);
		} else {
			$data=$this->_bdd->obtenirTous($requete);
			$result[]="";
			foreach ($data as $row)
			{
				$result[$row['id']]=$row['nom_compte'];
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

	function ajouter($idoperation,$idcompte,$idcategorie,$date_ecriture,$nom_frs,$montant,$description,
					$numero,$idmode_regl,$date_regl,$obs_regl,$idevenement, $numero_operation = null)
	{

		$requete = 'INSERT INTO ';
		$requete .= 'compta (';
		$requete .= 'idoperation,idcategorie,date_ecriture,nom_frs,montant,description,';
		$requete .= 'numero,idmode_regl,date_regl,obs_regl,idevenement, numero_operation,idcompte) ';
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
		$requete .= $this->_bdd->echapper($idevenement) . ',';
		$requete .= $this->_bdd->echapper($numero_operation) . ',';
		$requete .= $this->_bdd->echapper($idcompte) . ' ';
		$requete .= ');';

        $resultat = $this->_bdd->executer($requete);
        if ($resultat) {
            $this->lastId = $this->_bdd->obtenirDernierId();
        }
        return $resultat;
	}

	function modifier($id,$idoperation,$idcompte,$idcategorie,$date_ecriture,$nom_frs,$montant,$description,
					$numero,$idmode_regl,$date_regl,$obs_regl,$idevenement, $numero_operation = null)
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
		$requete .= 'idcompte='.$this->_bdd->echapper($idcompte) . ',';
        if ($numero_operation) {
    		$requete .= 'numero_operation='.$this->_bdd->echapper($numero_operation) . ',';
        }
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
		$requete .= ' compta_categorie.categorie,compta.date_ecriture ';

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



	function supprimerEcriture($id) {
		$requete = 'DELETE FROM compta WHERE id=' . $id;
		return $this->_bdd->executer($requete);
	}

    function obtenirParNumeroOperation($numero_operation)
    {
        $requete  = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  compta ';
        $requete .= 'WHERE numero_operation=' . $this->_bdd->echapper($numero_operation);

        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function obtenirSuivantADeterminer($numero_operation)
    {
        $requete  = 'SELECT';
        $requete .= '  id ';
        $requete .= 'FROM';
        $requete .= '  compta ';
        $requete .= 'WHERE ';
		$requete .= '  (';
		$requete .= '    idcategorie = 26 ';
		$requete .= '      OR ';
		$requete .= '    idevenement = 8';
		$requete .= '   )';
		$requete .= ' AND id > ' . $this->_bdd->echapper($numero_operation);
		$requete .= ' LIMIT 1;';
		return $this->_bdd->obtenirEnregistrement($requete);
    }

    function obtenirTous()
    {
        $requete  = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  compta ';

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirEvenementParIdForum($id)
    {
        $requete  = 'SELECT ';
        $requete .= '  compta_evenement.id ';
        $requete .= 'FROM ';
        $requete .= '  compta_evenement ';
        $requete .= 'INNER JOIN ';
        $requete .= '  afup_forum on afup_forum.titre = compta_evenement.evenement ';
        $requete .= 'WHERE ';
        $requete .= '  afup_forum.id = ' . (int) $id;
        return $this->_bdd->obtenirUn($requete);
    }

    /**
     *
     * @param array $csvFile
     */
    function extraireComptaDepuisCSVBanque($csvFile)
    {
        if (!is_array($csvFile) || !count($csvFile)) {
            return false;
        }
        // On vérifie la première ligne
        if (!substr($csvFile[0], 0, 17) == 'Code de la banque') {
            return false;
        }
        $forum = new AFUP_Forum($this->_bdd);
        $futurForum = $forum->obtenirDernier();
        $futurEvenement=$this->obtenirEvenementParIdForum($futurForum);
        // On efface les 4 premières lignes
        $csvFile = array_slice($csvFile, 4);
        foreach ($csvFile as $ligne) {
            $donnees = explode(';', $ligne);
            if (count($donnees) == 7) {
                $numero_operation = $donnees[1];
                // On vérife si l'enregistrement existe déjà
                $enregistrement = $this->obtenirParNumeroOperation($numero_operation);

                $date_ecriture = '20' . implode('-', array_reverse(explode('/', $donnees[0])));
                $description = $donnees[2] . '-' . $donnees[5];
                $donnees[3] = abs(str_replace(',', '.', $donnees[3]));
                $donnees[4] = abs(str_replace(',', '.', $donnees[4]));
                if ($donnees[4] == '') {
                    $idoperation = 1;
                    $montant = $donnees[3];
                } else {
                    $idoperation = 2;
                    $montant = $donnees[4];
                }
                // On tente les préaffectations
                $categorie = 26; // Catégorie 26 = "A déterminer"
                $evenement = 8;  // Evénement 8 = "A déterminer"
                if (strpos($donnees[5], 'CONTRAT 8316677013')) {
                    if ($idoperation == 2) {// CREDIT
                        // Virement PAYBOX
                        if ($montant < 100) {
                            // Vraisemblablement des cotisations
                            $categorie = 4;  // Catégorie 4 = "Cotisation AFUP"
                            $evenement = 27; // Evénement 27 = "Assocation AFUP"
                        } else {
                            // Vraisemblablement un réglement pour le prochain événement
                            $categorie = 3;  // Catégorie 3 = "Inscription"
                            $evenement = $futurEvenement;
                        }
                    } else {// DEBIT
                        // Commission PAYBOX
                        $categorie = 28; // Catégorie 28 = "Frais de compte"
                        $evenement = 26; // Evénement 26 = "Gestion"
                    }
                }
                $idmode_regl = 9;
                switch (strtoupper(substr($donnees[2], 0, 3))) {
                    case 'CB ':
                        $idmode_regl = 2;
                        break;
                    case 'VIR':
                        $idmode_regl = 3;
                        break;
                    case 'CHE':
                    case 'REM':
                        $idmode_regl = 4;
                        break;
                }

                if (!is_array($enregistrement)) {
                    $this->ajouter($idoperation, 1, $categorie, $date_ecriture, '', $montant, $description, '', $idmode_regl, $date_ecriture, '', $evenement, $numero_operation);
                } else {
                    $modifier = false;
                    if ($enregistrement['idcategorie'] == 26 && $categorie != 26) {
                        $enregistrement['idcategorie'] = $categorie;
                        $modifier = true;
                    }
                    if ($enregistrement['idevenement'] == 8 && $evenement != 8) {
                        $enregistrement['idevenement'] = $evenement;
                        $modifier = true;
                    }
                    if ($modifier) {
                        $this->modifier($enregistrement['id'],
                                        $enregistrement['idoperation'],
                                        1,
                                        $enregistrement['idcategorie'],
                                        $enregistrement['date_ecriture'],
                                        $enregistrement['nom_frs'],
                                        $enregistrement['montant'],
                                        $enregistrement['description'],
                                        $enregistrement['numero'],
                                        $enregistrement['idmode_regl'],
                                        $enregistrement['date_regl'],
                                        $enregistrement['obs_regl'],
                                        $enregistrement['idevenement'],
                                        $enregistrement['numero_operation']);
                    }
                }
            }
        }
        return true;
    }
}

?>
