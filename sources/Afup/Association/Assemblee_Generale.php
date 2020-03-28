<?php

// Voir la classe Afup\Site\Association\Assemblee_Generale
namespace Afup\Site\Association;
use Afup\Site\Utils\Mail;
use AppBundle\Association\Model\User;

define('AFUP_ASSEMBLEE_GENERALE_PRESENCE_INDETERMINE', 0);
define('AFUP_ASSEMBLEE_GENERALE_PRESENCE_OUI', 1);
define('AFUP_ASSEMBLEE_GENERALE_PRESENCE_NON', 2);

class Assemblee_Generale
{
    /**
     * @var \Afup\Site\Utils\Base_De_Donnees
     */
    var $_bdd;

    function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    public function hasGeneralMeetingPlanned(\DateTimeInterface $currentDate = null)
    {
        if (null === $currentDate) {
            $currentDate = new \DateTime();
        }

        $currentTimestamp = $currentDate->format('U');

        $timestamp = $this->obternirDerniereDate();

        return $timestamp > strtotime("-1 day", $currentTimestamp);
    }

    public function hasUserRspvedToLastGeneralMeeting(User $user)
    {
        $timestamp = $this->obternirDerniereDate();

        $infos = $this->obtenirToutesInfos($user->getUsername(), $timestamp);

        return isset($infos['date_modification']) && $infos['date_modification'] > 0;
    }

    function obternirDerniereDate()
    {
        $requete = 'SELECT';
        $requete .= '  MAX(date) ';
        $requete .= 'FROM';
        $requete .= '  afup_presences_assemblee_generale ';
        $requete .= 'LIMIT';
        $requete .= '  0, 1 ';
        return $this->_bdd->obtenirUn($requete);
    }

    function obtenirListe($date, $ordre = 'nom', $idPersonneAvecPouvoir = null) {
        $timestamp = convertirDateEnTimestamp($date);

        $requete = 'SELECT';
        $requete .= '  afup_personnes_physiques.id, ';
        $requete .= '  afup_personnes_physiques.email, ';
        $requete .= '  afup_personnes_physiques.login, ';
        $requete .= '  afup_personnes_physiques.nom, ';
        $requete .= '  afup_personnes_physiques.prenom, ';
        $requete .= '  afup_personnes_physiques.nearest_office, ';
        $requete .= '  afup_presences_assemblee_generale.date_consultation, ';
        $requete .= '  afup_presences_assemblee_generale.presence, ';
        $requete .= '  afup_personnes_avec_pouvoir.nom as personnes_avec_pouvoir_nom, ';
        $requete .= '  afup_personnes_avec_pouvoir.prenom as personnes_avec_pouvoir_prenom ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques, ';
        $requete .= '  afup_presences_assemblee_generale ';
        $requete .= 'LEFT JOIN';
        $requete .= '  afup_personnes_physiques as afup_personnes_avec_pouvoir ';
        $requete .= 'ON';
        $requete .= '  afup_personnes_avec_pouvoir.id = afup_presences_assemblee_generale.id_personne_avec_pouvoir ';
        $requete .= 'WHERE';
        $requete .= '  afup_presences_assemblee_generale.date = \'' . $timestamp . '\' ';
        $requete .= 'AND afup_presences_assemblee_generale.id_personne_physique = afup_personnes_physiques.id ';

        if (null !== $idPersonneAvecPouvoir) {
            $requete .= ' AND id_personne_avec_pouvoir = ' . $this->_bdd->echapper($idPersonneAvecPouvoir) . ' ';
        }

        $requete .= 'ORDER BY';
        $requete .= '  ' . $ordre . ' ';

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirPresents($timestamp, $options = array())
    {
        $requete = 'SELECT';
        $requete .= '  afup_personnes_physiques.id, ';
        $requete .= '  CONCAT(afup_personnes_physiques.nom, \' \', afup_personnes_physiques.prenom) as nom ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques, ';
        $requete .= '  afup_presences_assemblee_generale ';
        $requete .= 'WHERE';
        $requete .= '  afup_presences_assemblee_generale.date = \'' . $timestamp . '\' ';
        $requete .= 'AND afup_presences_assemblee_generale.presence = \'1\' ';
        $requete .= 'AND afup_personnes_physiques.id = afup_presences_assemblee_generale.id_personne_physique ';

        if (isset($options['exclure_login'])) {
            $requete .= "AND afup_personnes_physiques.login <> " . $this->_bdd->echapper($options['exclure_login']) . ' ';
        }

        $requete .= 'GROUP BY';
        $requete .= '  afup_personnes_physiques.id ';
        $requete .= 'ORDER BY afup_personnes_physiques.nom, afup_personnes_physiques.prenom ';

        return $this->_bdd->obtenirAssociatif($requete);
    }

    function obtenirNombreConvocations($timestamp)
    {
        $requete = 'SELECT';
        $requete .= '  COUNT(*) ';
        $requete .= 'FROM';
        $requete .= '  afup_presences_assemblee_generale ';
        $requete .= 'WHERE';
        $requete .= '  date = \'' . $timestamp . '\' ';
        return $this->_bdd->obtenirUn($requete);
    }

    function obtenirListePersonnesAJourDeCotisation($timestamp)
    {
        // On autorise un battement de 14 jours
        $timestamp -= 14 * 86400;
        // Personne physique seule
        $requete = 'SELECT';
        $requete .= '  app.id,app.id ';
        $requete .= 'FROM';
        $requete .= '  afup_cotisations ac ';
        $requete .= 'INNER JOIN';
        $requete .= '  afup_personnes_physiques app ON app.id = ac.id_personne ';
        $requete .= 'WHERE';
        $requete .= '  date_fin >= ' . $timestamp . ' ';
        $requete .= 'AND ';
        $requete .= '  type_personne = 0 ';
        $requete .= 'AND ';
        $requete .= '  etat = 1 ';
        $requete .= 'UNION ';
        $requete .= 'SELECT';
        $requete .= '  app.id,app.id ';
        $requete .= 'FROM';
        $requete .= '  afup_cotisations ac ';
        $requete .= 'INNER JOIN';
        $requete .= '  afup_personnes_physiques app ON app.id_personne_morale = ac.id_personne ';
        $requete .= 'WHERE';
        $requete .= '  date_fin >= ' . $timestamp . ' ';
        $requete .= 'AND ';
        $requete .= '  type_personne = 1 ';
        $requete .= 'AND ';
        $requete .= '  etat = 1 ';
        return array_values($this->_bdd->obtenirAssociatif($requete));
    }

    function obtenirNombrePresencesEtPouvoirs($timestamp)
    {
        $requete = 'SELECT';
        $requete .= '  COUNT(*) ';
        $requete .= 'FROM';
        $requete .= '  afup_presences_assemblee_generale ';
        $requete .= 'WHERE';
        $requete .= '  afup_presences_assemblee_generale.date = \'' . $timestamp . '\' ';
        $requete .= 'AND';
        $requete .= '   (afup_presences_assemblee_generale.presence = \'1\' ';
        $requete .= ' OR ';
        $requete .= '   afup_presences_assemblee_generale.id_personne_avec_pouvoir > 0) ';
        return $this->_bdd->obtenirUn($requete);
    }

    function obtenirNombrePresences($timestamp)
    {
        $requete = 'SELECT';
        $requete .= '  COUNT(*) ';
        $requete .= 'FROM';
        $requete .= '  afup_presences_assemblee_generale ';
        $requete .= 'WHERE';
        $requete .= '  afup_presences_assemblee_generale.date = \'' . $timestamp . '\' ';
        $requete .= 'AND';
        $requete .= '   afup_presences_assemblee_generale.presence = \'1\' ';
        return $this->_bdd->obtenirUn($requete);
    }

    function obtenirEcartQuorum($timestamp, $nombrePersonnesAJourDeCotisation)
    {
        $quorum = ceil($nombrePersonnesAJourDeCotisation / 3);
        $ecart = $this->obtenirNombrePresencesEtPouvoirs($timestamp) - $quorum;
        return $ecart;
    }

    function preparer($date, $description)
    {
        $requete = 'SELECT';
        $requete .= '  id ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE etat=1';
        $personnes_physiques = $this->_bdd->obtenirTous($requete);

        $timestamp = mktime(0, 0, 0, $date['m'], $date['d'], $date['Y']);

        $succes = false;
        if (is_array($personnes_physiques)) {
            $succes = 0;
            foreach ($personnes_physiques as $personne_physique) {
                $requete = 'SELECT';
                $requete .= '  id ';
                $requete .= 'FROM';
                $requete .= '  afup_presences_assemblee_generale ';
                $requete .= 'WHERE';
                $requete .= '  id_personne_physique = ' . $personne_physique['id'] . ' ';
                $requete .= 'AND';
                $requete .= '  date = ' . $timestamp;
                $preparation = $this->_bdd->obtenirUn($requete);
                if (!$preparation) {
                    $requete = 'INSERT INTO ';
                    $requete .= '  afup_presences_assemblee_generale (id_personne_physique, date) ';
                    $requete .= 'VALUES (';
                    $requete .= $personne_physique['id'] . ',';
                    $requete .= $timestamp . ')';
                    $succes += $this->_bdd->executer($requete);
                }
            }
        }

        if (!$succes) {
            return $succes;
        }

        $requete = <<<EOF
REPLACE INTO afup_assemblee_generale (`date`, `description`)
VALUES ({date}, {description})
EOF;

        $requete = strtr(
            $requete,
            [
                '{date}' => $timestamp,
                '{description}' => $this->_bdd->echapper($description)
            ]
        );

        $succes += $this->_bdd->executer($requete);

        return $succes;
    }

    public function ajouter($idPersonnePhysique, $timestamp, $presence, $id_personne_avec_pouvoir)
    {
        $requete = <<<EOF
INSERT INTO afup_presences_assemblee_generale (id_personne_physique, `date`, presence, id_personne_avec_pouvoir, date_modification) 
VALUES ({id_personne_physique}, {date}, {presence}, {id_personne_avec_pouvoir}, {date_modification})
EOF;

        $requete = strtr(
            $requete,
            [
                '{id_personne_physique}' => $this->_bdd->echapper($idPersonnePhysique),
                '{date}' => $this->_bdd->echapper($timestamp),
                '{presence}' => $this->_bdd->echapper($presence),
                '{id_personne_avec_pouvoir}' => $this->_bdd->echapper((int) $id_personne_avec_pouvoir),
                '{date_modification}' => time(),
            ]
        );

        return $this->_bdd->executer($requete);
    }

    function modifier($login, $timestamp, $presence, $id_personne_avec_pouvoir)
    {
        $requete = 'UPDATE ';
        $requete .= '  afup_presences_assemblee_generale, ';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'SET';
        $requete .= '  afup_presences_assemblee_generale.presence = ' . $this->_bdd->echapper((int)$presence) . ',';
        $requete .= '  afup_presences_assemblee_generale.id_personne_avec_pouvoir = ' . $this->_bdd->echapper((int)$id_personne_avec_pouvoir) . ',';
        $requete .= '  afup_presences_assemblee_generale.date_modification = ' . time() . ' ';
        $requete .= 'WHERE';
        $requete .= '  afup_presences_assemblee_generale.id_personne_physique = afup_personnes_physiques.id ';
        $requete .= 'AND (afup_personnes_physiques.login = ' . $this->_bdd->echapper($login) . ' OR afup_personnes_physiques.email = ' . $this->_bdd->echapper($login) . ' )';
        $requete .= 'AND afup_presences_assemblee_generale.date = ' . $timestamp;

        return $this->_bdd->executer($requete);
    }

    function obtenirInfos($login, $timestamp)
    {
        $requete = 'SELECT';
        $requete .= '  afup_presences_assemblee_generale.presence, ';
        $requete .= '  afup_presences_assemblee_generale.id_personne_avec_pouvoir ';
        $requete .= 'FROM';
        $requete .= '  afup_presences_assemblee_generale, ';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE';
        $requete .= '  afup_presences_assemblee_generale.id_personne_physique = afup_personnes_physiques.id ';
        $requete .= 'AND afup_personnes_physiques.login = ' . $this->_bdd->echapper($login) . ' ';
        $requete .= 'AND afup_presences_assemblee_generale.date = ' . $timestamp . ' ';
        $requete .= 'LIMIT 0, 1';

        $infos = $this->_bdd->obtenirEnregistrement($requete, MYSQLI_NUM);

        return $infos;
    }

    public function obtenirDescription($timestamp)
    {
        $requete = <<<EOF
SELECT description
FROM afup_assemblee_generale
WHERE `date` = {timestamp}
EOF;

        $requete = strtr(
            $requete,
            [
                '{timestamp}' => $timestamp,
            ]
        );

        $infos = $this->_bdd->obtenirEnregistrement($requete);
        
        if (false === $infos) {
            return null;
        }

        return $infos['description'];
    }

    public function obtenirListeAssembleesGenerales()
    {
        $requete = <<<EOF
SELECT DISTINCT afup_presences_assemblee_generale.date
FROM afup_presences_assemblee_generale
ORDER BY afup_presences_assemblee_generale.date DESC
EOF;

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirToutesInfos($login, $timestamp)
    {
        $requete = 'SELECT';
        $requete .= '  afup_presences_assemblee_generale.* ';
        $requete .= 'FROM';
        $requete .= '  afup_presences_assemblee_generale, ';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE';
        $requete .= '  afup_presences_assemblee_generale.id_personne_physique = afup_personnes_physiques.id ';
        $requete .= 'AND afup_personnes_physiques.login = ' . $this->_bdd->echapper($login) . ' ';
        $requete .= 'AND afup_presences_assemblee_generale.date = ' . $timestamp . ' ';
        $requete .= 'LIMIT 0, 1';

        $infos = $this->_bdd->obtenirEnregistrement($requete, MYSQLI_ASSOC);

        return $infos;
    }

    function obtenirListeEmailPersonnesAJourDeCotisation()
    {
        // On autorise un battement de 2 mois pour l'envoi d'email
        $timestamp = time() - 60 * 86400;
        // Personne physique seule
        $requete = "SELECT group_concat(DISTINCT email SEPARATOR ';')
                    FROM (
                    SELECT app.email
                    FROM afup_cotisations ac
                    INNER JOIN afup_personnes_physiques app ON app.id = ac.id_personne
                    WHERE date_fin >= " . $timestamp . "
                    AND type_personne = 0
                    AND etat = 1
                    UNION
                    SELECT app.email
                    FROM afup_cotisations ac
                    INNER JOIN afup_personnes_physiques app ON app.id_personne_morale = ac.id_personne
                    WHERE date_fin >= " . $timestamp . "
                    AND type_personne = 1
                    AND etat = 1 ) tmp";
        return $this->_bdd->obtenirUn($requete);
    }
}
