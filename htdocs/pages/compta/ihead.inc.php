<?php
require_once("include/config.inc.php");

if (isset($_GET['idperiode']) && $_GET['idperiode']>0)
{
if (isset($_GET['idperiode']))
    $idperiode=verif_GetPost($_GET['idperiode']);
else
    $idperiode=3;


$rep=recup_periode($idperiode);

$periode_debut=$rep[0];
$periode_fin=$rep[1];
}

?>

<table width="100%">
<tr><td>Compta<br>
<table border=1 width=80% align="center">
<tr><td valign="top">
<a href="compta-saisie.php?idperiode=<?php echo $idperiode; ?>" class="links">Saisie compta</a><br>
<a href="compta-saisie-view.php?idperiode=<?php echo $idperiode; ?>" class="links">Voir Journal compta</a><br>
<a href="compta-banque-view.php?compte=espece&idperiode=<?php echo $idperiode; ?>" class="links">Voir Journal Compte Espece</a><br>
<a href="compta-banque-view.php?compte=paypal&idperiode=<?php echo $idperiode; ?>" class="links">Voir Journal Compte Paypal</a><br>
<a href="compta-banque-view.php?compte=banque&idperiode=<?php echo $idperiode; ?>" class="links">Voir Journal Compte Courant</a><br>
<a href="compta-banque-view.php?compte=livret&idperiode=<?php echo $idperiode; ?>" class="links">Voir Journal Compte Livret A</a><br>
<a href="compta-synthese.php?idperiode=<?php echo $idperiode; ?>" class="links">Synthese evenement</a><br>
<a href="compta-balance.php?idperiode=<?php echo $idperiode; ?>" class="links">Balance</a><br>
<a href="compta-bilan.php?idperiode=<?php echo $idperiode; ?>" class="links">Bilan</a><br>
</td><td>Parametres<br>
<a href="compta-evenement.php?idperiode=<?php echo $idperiode; ?>" class="links">Evenement</a><br>
<a href="compta-categorie.php?idperiode=<?php echo $idperiode; ?>" class="links">Categorie</a><br>
<a href="compta-operation.php?idperiode=<?php echo $idperiode; ?>" class="links">Operation</a><br>
<a href="compta-reglement.php?idperiode=<?php echo $idperiode; ?>" class="links">Mode reglement</a><br>
</td></tr></table>
</td>
<td valign="top">Budget<br>
<table border=1 width=80% align="center">
<tr><td>
<a href="budget-saisie.php?idperiode=<?php echo $idperiode; ?>" class="links">Saisie budget</a><br>
<a href="budget-view.php?idperiode=<?php echo $idperiode; ?>" class="links">Voir Bilan budget</a><br>
<a href="budget-rapprochement.php?idperiode=<?php echo $idperiode; ?>" class="links">Rapprochement budget/compta</a><br>
<br>
<a href="compta-simul-saisie.php?idperiode=<?php echo $idperiode; ?>" class="links">Forum Simulation</a><br>
<a href="compta-simul-view.php?idperiode=<?php echo $idperiode; ?>" class="links">Voir Simulation</a><br>
</td><td>Parametres<br>
<a href="budget-evenement.php?idperiode=<?php echo $idperiode; ?>" class="links">Evenement</a><br>
<a href="budget-categorie.php?idperiode=<?php echo $idperiode; ?>" class="links">Categorie</a><br>
<a href="budget-operation.php?idperiode=<?php echo $idperiode; ?>" class="links">Operation</a><br>
</td></tr></table>
<a href="facture.php" class="links">Facture</a><br>
<a href="devis.php" class="links">Devis</a><br>

</td></tr></table>
<?php
echo "Période : ";

echo "<form action=".htmlentities($_SERVER['PHP_SELF'])."  method='GET'>";
$cnx9 = new PDO('mysql:host='.$serveur.';port='.$port.';dbname='.$bdd, $user, $passwd);

$sql9="SELECT * FROM compta_periode ORDER BY date_debut";
$even9=$cnx9->prepare($sql9);
$even9->execute();


        echo "<select name='idperiode'>";

        while( $row9=$even9->fetch(PDO::FETCH_ASSOC) )
        {
                echo ligne_selected($row9[date_debut]." au ".$row9[date_fin],$row9['id'],$idperiode);
        }
        echo "</select>";
        $cnx9=NULL;
        echo "<input type='submit' name='action' value='ok'>";
echo "</form>";


?>