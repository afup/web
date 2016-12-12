<?php

// Voir la classe Afup\Site\Association\Assemblee_Generale
namespace Afup\Site\Association;
use Afup\Site\Utils\Mail;

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

    function obtenirListe($date,
                          $ordre = 'nom')
    {
        $timestamp = convertirDateEnTimestamp($date);

        $requete = 'SELECT';
        $requete .= '  afup_personnes_physiques.id, ';
        $requete .= '  afup_personnes_physiques.email, ';
        $requete .= '  afup_personnes_physiques.login, ';
        $requete .= '  afup_personnes_physiques.nom, ';
        $requete .= '  afup_personnes_physiques.prenom, ';
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

    function obtenirNombrePersonnesAJourDeCotisation($timestamp)
    {
        // On autorise un battement de 14 jours
        $timestamp -= 14 * 86400;
        // Personne physique seule
        $requete = 'SELECT';
        $requete .= '  COUNT(*) ';
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
        $physiques = $this->_bdd->obtenirUn($requete);
        // Personne morale
        $requete = 'SELECT';
        $requete .= '  COUNT(*) ';
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
        $morales = $this->_bdd->obtenirUn($requete);
        return $physiques + $morales;
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

    function obtenirEcartQuorum($timestamp)
    {
        $quorum = ceil($this->obtenirNombrePersonnesAJourDeCotisation($timestamp) / 3);
        $ecart = $this->obtenirNombrePresencesEtPouvoirs($timestamp) - $quorum;
        return $ecart;
    }

    function preparer($date)
    {
        $requete = 'SELECT';
        $requete .= '  id ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE etat=1';
        $personnes_physiques = $this->_bdd->obtenirTous($requete);

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
                $requete .= '  date = ' . mktime(0, 0, 0, $date['m'], $date['d'], $date['Y']);
                $preparation = $this->_bdd->obtenirUn($requete);
                if (!$preparation) {
                    $requete = 'INSERT INTO ';
                    $requete .= '  afup_presences_assemblee_generale (id_personne_physique, date) ';
                    $requete .= 'VALUES (';
                    $requete .= $personne_physique['id'] . ',';
                    $requete .= mktime(0, 0, 0, $date['m'], $date['d'], $date['Y']) . ')';
                    $succes += $this->_bdd->executer($requete);
                }
            }
        }
        return $succes;

    }

    function marquerConsultation($login, $timestamp)
    {
        $requete = 'UPDATE ';
        $requete .= '  afup_presences_assemblee_generale, ';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'SET';
        $requete .= '  afup_presences_assemblee_generale.date_consultation = ' . time() . ' ';
        $requete .= 'WHERE';
        $requete .= '  afup_presences_assemblee_generale.id_personne_physique = afup_personnes_physiques.id ';
        $requete .= 'AND afup_personnes_physiques.login = ' . $this->_bdd->echapper($login) . ' ';
        $requete .= 'AND afup_presences_assemblee_generale.date_consultation = \'0\'';

        return $this->_bdd->executer($requete);
    }

    function preparerCorpsDuMessage($timestamp)
    {
        $corps = "La prochaine assemblée générale de l'AFUP aura lieu le " . date('d/m/Y', $timestamp) . ".\n\n";
        $corps .= "Cette AG se tiendra, à partir de 18h30, à la Maison des Associations Solidaires à Paris. ";
        $corps .= "Elle est située au 10/18 rue des terres au curé, Paris XIIIème.\n\n";

        $corps .= "Merci de bien vouloir cliquer sur le lien ci-dessous : ";
        $corps .= "il nous sert d'accusé de réception de cette convocation, ";
        $corps .= "il vous permet d'indiquer votre présence ";
        $corps .= "et - le cas échéant - à qui vous souhaitez transmettre votre pouvoir.\n\n";

        $corps .= "Nous vous rappelons que seuls les membres à jour de cotisation peuvent participer et voter lors de l'assemblée générale.\n\n";
        return $corps;
    }

    function preparerSujetDuMessage($timestamp)
    {
        $sujet = "AFUP : convocation à l'assemblée générale du " . date('d/m/Y', $timestamp);

        return $sujet;
    }

    function envoyerConvocations($timestamp, $sujet, $corps)
    {
        $requete = 'SELECT';
        $requete .= '  afup_personnes_physiques.id, ';
        $requete .= '  afup_personnes_physiques.email, ';
        $requete .= '  afup_personnes_physiques.login, ';
        $requete .= '  CONCAT(afup_personnes_physiques.nom, \' \', afup_personnes_physiques.prenom) as nom ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques, ';
        $requete .= '  afup_presences_assemblee_generale ';
        $requete .= 'WHERE';
        $requete .= '  afup_presences_assemblee_generale.date = \'' . $timestamp . '\' ';
        $requete .= 'AND afup_personnes_physiques.id = afup_presences_assemblee_generale.id_personne_physique ';
        $requete .= 'AND afup_presences_assemblee_generale.presence = 0 ';
        $requete .= 'AND afup_presences_assemblee_generale.id_personne_avec_pouvoir = 0 ';
        $requete .= 'GROUP BY';
        $requete .= '  afup_personnes_physiques.id ';
        $personnes_physiques = $this->_bdd->obtenirTous($requete);

        $succes = false;
        foreach ($personnes_physiques as $personne_physique) {
            $hash = md5($personne_physique['id'] . '_' . $personne_physique['email'] . '_' . $personne_physique['login']);
            $link = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . '?hash=' . $hash;

            $mail = new Mail();

            if ($mail->send(
                'message-transactionnel-afup-org',
                ['email' => $personne_physique['email'], 'name' => $personne_physique['nom']],
                [
                    'content' => $corps . '<p><a href="' . $link. '">' . $link . '</a></p>',
                    'title' => 'Assemblée Générale'
                ],
                ['subject' => $sujet]
            )) {
                $succes += 1;
            }
        }

        return $succes;
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
        $requete .= 'AND afup_personnes_physiques.login = ' . $this->_bdd->echapper($login) . ' ';
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

        $infos = $this->_bdd->obtenirEnregistrement($requete, MYSQL_NUM);

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
