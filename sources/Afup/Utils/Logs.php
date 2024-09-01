<?php

namespace Afup\Site\Utils;

/**
 * Classe de gestion des logs
 */
class Logs
{
    /**
     * Instance de la couche d'abstraction à la base de données
     * @var     object
     * @access  private
     */
    private $_bdd;

    /**
     * Identifiant de l'utilisateur connecté
     * @var     int
     * @access  private
     */
    private $_id_utilisateur;

    /**
     * Nombre de logs affichés par page
     * @var     int
     * @access  private
     */
    private $_nombre_logs_par_page = 15; // TODO : Utiliser une constante en PHP5

    /**
     * Renvoit l'instance unique de la classe Afup\Site\Utils\Logs
     *
     * Cette fonction est une implémentation du pattern Singleton.
     * Cela permet d'appeller statiquement les méthodes de cette classe depuis n'importe où.
     *
     * @access private
     * @return object   Instance de la classe Afup\Site\Utils\Logs
     */
    static function &_obtenirInstance()
    {
        // TODO : Utiliser une propriété statique en PHP5
        if (!isset($GLOBALS['_afup_log'])) {
            $GLOBALS['_afup_log'] = new Logs;
        }
        return $GLOBALS['_afup_log'];
    }

    /**
     * Initialise les propriétés de la classe
     *
     * @param  object $bdd Instance de la couche d'abstraction à la base de données
     * @param  int $id_utilisateur Identifiant de l'utilisateur connecté
     * @access public
     * @return void
     */
    static function initialiser(&$bdd, $id_utilisateur)
    {
        $instance =& Logs::_obtenirInstance();
        $instance->_bdd =& $bdd;
        $instance->_id_utilisateur = $id_utilisateur;
    }

    /**
     * Log le texte fourni
     *
     * @param  string $texte Texte à logger
     * @access public
     * @return void
     */
    static function log($texte)
    {
        $instance =& Logs::_obtenirInstance();
        $requete = 'INSERT INTO';
        $requete .= '  afup_logs (id, date, id_personne_physique, texte) ';
        $requete .= 'VALUES (';
        $requete .= '  NULL,';
        $requete .= '  ' . time() . ',';
        $requete .= '  ' . $instance->_id_utilisateur . ',';
        $requete .= '  ' . $instance->_bdd->echapper($texte);
        $requete .= ')';
        $instance->_bdd->Executer($requete);
    }

    /**
     * Renvoit tous les logs de la page indiquée
     *
     * @param  int $numero_page Numéro de la page concernée
     * @access public
     * @return array    Les logs correspondant à la page indiquée
     */
    static function obtenirTous($numero_page)
    {
        $instance =& Logs::_obtenirInstance();
        $depart = ($numero_page - 1) * $instance->_nombre_logs_par_page;
        $requete = 'SELECT';
        $requete .= '  afup_logs.*,';
        $requete .= '  IF(afup_personnes_physiques.nom != "", afup_personnes_physiques.nom, "BOT") as nom,';
        $requete .= '  afup_personnes_physiques.prenom ';
        $requete .= 'FROM';
        $requete .= '  afup_logs';
        $requete .= '  LEFT JOIN afup_personnes_physiques';
        $requete .= '  ON afup_personnes_physiques.id=afup_logs.id_personne_physique ';
        $requete .= 'ORDER BY';
        $requete .= '  afup_logs.date DESC ';
        $requete .= 'LIMIT';
        $requete .= '  ' . $depart . ', ' . $instance->_nombre_logs_par_page;
        return $instance->_bdd->obtenirTous($requete);
    }

    /**
     * Renvoit le nombre de pages de logs
     *
     * @access public
     * @return int  Nombre de pages
     */
    static function obtenirNombrePages()
    {
        $instance =& Logs::_obtenirInstance();
        $nombre = $instance->_bdd->obtenirUn('SELECT COUNT(*) FROM afup_logs');
        $nombre = ceil($nombre / $instance->_nombre_logs_par_page);
        return ($nombre == 0) ? 1 : $nombre;
    }
}

?>
