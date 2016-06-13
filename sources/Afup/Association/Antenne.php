<?php
//@TODO
// Ajout période comptable automatiquement
// revoir sous totaux balance
// test champ obligatoire lors de la saisie
// ajout filtre par mois pour les journaux banques
namespace Afup\Site\Association;

class Antenne
{
	/**
	 * @var \Afup\Site\Utils\Base_De_Donnees
	 */
    var $_bdd;
    public $lastId = null;

    function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }


    function obtenirListAntennes($filtre = '', $where = '')
    {
        $requete = 'SELECT ';
        $requete .= 'id, ville ';
        $requete .= 'FROM  ';
        $requete .= 'afup_antenne  ';
        if ($where) $requete .= 'WHERE id=' . $where . ' ';

        $requete .= 'ORDER BY ';
        $requete .= 'ville ';

        if ($where) {
            return $this->_bdd->obtenirEnregistrement($requete);
        } elseif ($filtre) {
            return $this->_bdd->obtenirTous($requete);
        } else {
            $data = $this->_bdd->obtenirTous($requete);
            $result[] = "";
            foreach ($data as $row) {
                $result[$row['id']] = $row['ville'];
            }

            return $result;
        }
    }


    function ajouter($table, $champ, $valeur)
    {
        $requete = 'INSERT INTO ';
        $requete .= '' . $table . ' (';
        $requete .= '' . $champ . ') ';
        $requete .= 'VALUES (';
        $requete .= $this->_bdd->echapper($valeur) . ' ';
        $requete .= ');';

        return $this->_bdd->executer($requete);
    }

    function modifier($table, $id, $champ, $valeur)
    {

        $requete = 'UPDATE ';
        $requete .= '' . $table . ' ';
        $requete .= 'SET ';
        $requete .= '' . $champ . ' = ' . $this->_bdd->echapper($valeur) . ' ';
        $requete .= 'WHERE ';
        $requete .= 'id = ' . $id . ' ';

        return $this->_bdd->executer($requete);
    }


}

?>
