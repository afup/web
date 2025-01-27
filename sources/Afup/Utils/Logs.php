<?php

declare(strict_types=1);

namespace Afup\Site\Utils;

/**
 * Classe de gestion des logs
 */
class Logs
{
    // TODO : Utiliser une constante en PHP5

    /**
     * Renvoit l'instance unique de la classe Afup\Site\Utils\Logs
     *
     * Cette fonction est une implémentation du pattern Singleton.
     * Cela permet d'appeller statiquement les méthodes de cette classe depuis n'importe où.
     *
     * @return object   Instance de la classe Afup\Site\Utils\Logs
     */
    public static function &_obtenirInstance()
    {
        // TODO : Utiliser une propriété statique en PHP5
        if (!isset($GLOBALS['_afup_log'])) {
            $GLOBALS['_afup_log'] = new self;
        }
        return $GLOBALS['_afup_log'];
    }

    /**
     * Initialise les propriétés de la classe
     *
     * @param  object $bdd Instance de la couche d'abstraction à la base de données
     * @param  int $id_utilisateur Identifiant de l'utilisateur connecté
     */
    public static function initialiser(&$bdd, $id_utilisateur): void
    {
        $instance =& self::_obtenirInstance();
        $instance->_bdd =& $bdd;
        $instance->_id_utilisateur = $id_utilisateur;
    }

    /**
     * Log le texte fourni
     *
     * @param  string $texte Texte à logger
     */
    public static function log($texte): void
    {
        $instance =& self::_obtenirInstance();
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
     * @return array    Les logs correspondant à la page indiquée
     */
    public static function obtenirTous($numero_page)
    {
        $instance =& self::_obtenirInstance();
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
     * @return int  Nombre de pages
     */
    public static function obtenirNombrePages(): int
    {
        $instance =& self::_obtenirInstance();
        $nombre = $instance->_bdd->obtenirUn('SELECT COUNT(*) FROM afup_logs');
        if (!$instance->_nombre_logs_par_page) {
            return 1;
        }
        $nombre = ceil($nombre / $instance->_nombre_logs_par_page);
        return $nombre === 0.0 ? 1 : (int) $nombre;
    }
}
