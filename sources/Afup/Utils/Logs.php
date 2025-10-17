<?php

declare(strict_types=1);

namespace Afup\Site\Utils;

use Afup\Site\Corporate\_Site_Base_De_Donnees;

/**
 * Classe de gestion des logs
 */
class Logs
{
    private int $_nombre_logs_par_page = 10;

    public function __construct(
        private readonly _Site_Base_De_Donnees $_bdd,
        private readonly int $_id_utilisateur,
    ) {}

    /**
     * Renvoit l'instance unique de la classe Afup\Site\Utils\Logs
     *
     * Cette fonction est une implémentation du pattern Singleton.
     * Cela permet d'appeller statiquement les méthodes de cette classe depuis n'importe où.
     */
    public static function &_obtenirInstance(): self
    {
        // TODO : Utiliser une propriété statique en PHP5
        if (!isset($GLOBALS['_afup_log'])) {
            throw new \RuntimeException("The logs instance has not been initialized");
        }

        return $GLOBALS['_afup_log'];
    }

    /**
     * Initialise les propriétés de la classe
     *
     * @param  _Site_Base_De_Donnees $bdd Instance de la couche d'abstraction à la base de données
     * @param  int $id_utilisateur Identifiant de l'utilisateur connecté
     */
    public static function initialiser(&$bdd, $id_utilisateur): void
    {
        $GLOBALS['_afup_log'] = new self($bdd, $id_utilisateur);
    }

    /**
     * Log le texte fourni
     *
     * @param  string $texte Texte à logger
     */
    public static function log($texte): void
    {
        $instance = & self::_obtenirInstance();
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
}
