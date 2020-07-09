<?php
namespace Afup\Site\Utils;

define('EURO', '€');

/**
 * Classe de gestion de la configuration
 */
class Configuration
{
    /**
     * Chemin vers le fichier de configuration
     * @var     string
     * @access  private
     */
    var $_chemin_fichier;

    /**
     * Valeurs de configuration
     * @var     array
     * @access  private
     */
    var $_valeurs;

    /**
     * Constructeur. Charge les valeurs depuis le fichier de configuration
     *
     * @param string $chemin_fichier Chemin vers le fichier de configuration
     * @access public
     * @return void
     */
    public function __construct($chemin_fichier)
    {
        $this->_chemin_fichier = $chemin_fichier;
        include $this->_chemin_fichier;
        $this->_valeurs = $configuration;
        unset($configuration);
    }

    /**
     * Transforme un tableau associatif en un tableau de chemins.
     *
     * Cette méthode effectue la transformation inverse de celle effectuée par la méthode Afup\Site\Utils\Configuration::_genererValeurs.
     *
     * Exemple de fonctionnement :
     * $tableau = array('a' => 'pomme',
     *                  'b' => array('ba' => 'poire'),
     *                               'bb' => 'fraise');
     *
     * $chemins = array('a'    => 'pomme',
     *                  'b|ba' => 'poire',
     *                  'b|bb' => 'fraise');
     *
     * @param array     $tableau     Tableau associatif à transformer
     * @param array     $chemins     Tableau associatif contenant les chemins générés
     * @param string    $parent      Chemin du parent. Cette information est utilisée lors de l'appel récursif de cette méthode.
     * @access private
     * @return void
     * @see AFUP_Configuration::_genererValeurs
     */
    public function _genererChemins($tableau, &$chemins, $parent = '')
    {
        foreach ($tableau as $cle => $valeur) {
            if (is_array($valeur)) {
                $this->_genererChemins($valeur, $chemins, $parent . '|' . $cle);
            } else {
                if ($parent == '') {
                    $chemins[$cle] = $valeur;
                } else {
                    $chemins[substr($parent, 1) . '|' . $cle] = $valeur;
                }
            }
        }
    }

    /**
     * Transforme un tableau de chemins en un tableau associatif.
     *
     * Cette méthode effectue la transformation inverse de celle effectuée par la méthode Afup\Site\Utils\Configuration::_genererChemins.
     *
     * Exemple de fonctionnement :
     * $chemins = array('a'    => 'pomme',
     *                  'b|ba' => 'poire',
     *                  'b|bb' => 'fraise');
     *
     * $tableau = array('a' => 'pomme',
     *                  'b' => array('ba' => 'poire'),
     *                               'bb' => 'fraise');
     *
     * @param array     $tableau     Tableau associatif à transformer
     * @param array     $valeurs     Tableau associatif contenant les valeurs
     * @param string    $parent      Chemin du parent. Cette information est utilisée lors de l'appel récursif de cette méthode.
     * @access private
     * @return void
     * @see AFUP_Configuration::_genererChemins
     */
    function _genererValeurs($tableau, &$valeurs, $parent = '')
    {
        foreach ($tableau as $cle => $valeur) {
            if (is_array($valeur)) {
                $this->_genererValeurs($valeur, $valeurs, $parent . "['{$cle}']");
            } else {
                if (is_string($valeur)) {
                    $valeur = "'" . str_replace("'", "\'", $valeur) . "'";
                }
                $valeurs[] = $parent . "['{$cle}']={$valeur};";
            }
        }
    }

    /**
     * Renvoie la valeur correspondant à la clé
     *
     * @param string $cle Clé
     * @access public
     * @return mixed    Valeur correspondant à la clé
     */
    function obtenir($cle)
    {

        eval('$valeur=$this->_valeurs["' . str_replace('|', '"]["', $cle) . '"];');
        return $valeur;
    }

    /**
     * Renvoit les valeurs de configuration sous la forme d'un tableau de ce type :
     *
     * $chemins = array('a'    => 'pomme',
     *                  'b|ba' => 'poire',
     *                  'b|bb' => 'fraise');
     *
     * @access public
     * @return array    Tableau contenant les valeurs de configuration
     */
    function exporter()
    {
        $chemins = array();
        $this->_genererChemins($this->_valeurs, $chemins);
        return $chemins;
    }

    /**
     * Met à jour les valeurs de configuration depuis un tableau de ce type :
     *
     * $chemins = array('a'    => 'pomme',
     *                  'b|ba' => 'poire',
     *                  'b|bb' => 'fraise');
     *
     * @param   array $valeurs Valeurs à importer
     * @access public
     * @return void
     */
    function importer($valeurs)
    {
        foreach ($valeurs as $cle => $valeur) {
            $code = '$this->_valeurs';
            $etages = explode('|', $cle);
            foreach ($etages as $etage) {
                $code .= "['{$etage}']";
            }
            $code .= "='" . str_replace("'", "\'", $valeur) . "';";
            eval($code);
        }
    }

    /**
     * Enregistre les valeurs dans le fichier de configuration
     *
     * @access public
     * @return bool
     */
    function enregistrer()
    {
        $valeurs = array();
        $this->_genererValeurs($this->_valeurs, $valeurs);
        $contenu = "<?php\n";
        foreach ($valeurs as $valeur) {
            $contenu .= '$configuration' . $valeur . "\n";
        }
        $contenu .= '?>';

        if (!is_writable($this->_chemin_fichier)) {
            return false;
        }
        if (!$pointeur = fopen($this->_chemin_fichier, 'w')) {
            return false;
        }
        if (fwrite($pointeur, $contenu) === false) {
            return false;
        }
        fclose($pointeur);
        return true;
    }
}
