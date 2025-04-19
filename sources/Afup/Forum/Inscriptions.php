<?php

declare(strict_types=1);

namespace Afup\Site\Forum;

use Afup\Site\Utils\Base_De_Donnees;

class Inscriptions
{
    /**
     * Instance de la couche d'abstraction ï¿½ la base de donnï¿½es
     * @var     Base_De_Donnees
     */
    private $_bdd;

    /**
     * Constructeur.
     *
     * @param  object $bdd Instance de la couche d'abstraction ï¿½ la base de donnï¿½es
     * @return void
     */
    public function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    /**
     * Renvoit les informations concernant une inscription
     *$inscrits =
     * @param  int $id Identifiant de la personne
     * @param  string $champs Champs ï¿½ renvoyer
     * @return array
     */
    public function obtenir($id, string $champs = 'i.*')
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_inscription_forum i ';
        $requete .= 'LEFT JOIN';
        $requete .= '  afup_facturation_forum f ON i.reference = f.reference ';

        $requete .= 'WHERE i.id=' . intval($id);
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    /**
     * Renvoie la liste des inscriptions pour lesquels md5(concat('id', 'reference')) = $code_md5 (1er argument)
     * (concaténation des champs 'id' et 'reference', passée à la fonction md5)
     *
     * @param $code_md5 string Md5 de la concaténation des champs "id" et "reference"
     * @param string $champs Liste des champs à récupérer en BD
     * @return array
     */
    public function obtenirInscription($code_md5, $champs = 'i.*')
    {
        $requete = "SELECT $champs FROM afup_inscription_forum i ";
        $requete .= "LEFT JOIN afup_facturation_forum f ON i.reference = f.reference ";
        $requete .= "WHERE md5(CONCAT(i.id, i.reference)) = '$code_md5'";

        return $this->_bdd->obtenirEnregistrement($requete);
    }

    /**
     * Retrieve the registrations associated to the same reference
     * <p>Used by example to send a confirmation email to every people associated
     * to the same payment.</p>
     * @param string $reference The reference shared
     * @return array The people we want ;)
     */
    public function getRegistrationsByReference($reference)
    {
        $ref = $this->_bdd->echapper($reference);
        $sql = <<<SQL
SELECT *
FROM afup_inscription_forum
WHERE reference = $ref;
SQL;
        return $this->_bdd->obtenirTous($sql);
    }

    /**
     * @param int $idForum
     * @param int $idForumPrecedent
     */
    public function obtenirSuivi($idForum, $idForumPrecedent = null): array
    {
        $forum = new Forum($this->_bdd);
        if (null === $idForumPrecedent) {
            $idForumPrecedent = $forum->obtenirForumPrecedent($idForum);
        }

        $now = new \DateTime();
        $dateForum = new \DateTimeImmutable('@' . $forum->obtenir($idForum)['date_fin_vente']);

        $daysToEndOfSales = 0;
        if ($dateForum >= $now) {
            $daysToEndOfSales = $dateForum->diff($now)->format('%r%a');
        }

        $requete = 'SELECT 
          COUNT(*) as nombre, 
          DATEDIFF(FROM_UNIXTIME(date, \'%Y-%m-%d\'), FROM_UNIXTIME(af.date_fin_vente, \'%Y-%m-%d\')) as jour,
          id_forum
        FROM
          afup_inscription_forum i
        RIGHT JOIN afup_forum_tarif aft ON (aft.id = i.type_inscription AND aft.default_price > 0)
        LEFT JOIN afup_forum af ON af.id = i.id_forum
        WHERE
          i.id_forum IN (' . (int) $idForum . ', ' . (int) $idForumPrecedent . ') 
        AND 
          etat <> 1 
        GROUP BY jour, i.id_forum 
        HAVING jour < 0
        ORDER BY jour ASC ';
        $nombre_par_date = $this->_bdd->obtenirTous($requete);

        if ([] === $nombre_par_date) {
            $nombre_par_date = [['jour' => 1]];
        }

        $suivis = [];

        for ($i = $nombre_par_date[0]['jour']; $i <= 0; $i++) {
            $infoForum = array_sum(array_map(function (array $info) use ($i, $idForum) {
                if ($info['id_forum'] == $idForum && $info['jour'] <= $i) {
                    return $info['nombre'];
                }
                return 0;
            }, $nombre_par_date));
            $infoN1 = array_sum(array_map(function (array $info) use ($i, $idForumPrecedent) {
                if ($info['id_forum'] == $idForumPrecedent && $info['jour'] <= $i) {
                    return $info['nombre'];
                }
                return 0;
            }, $nombre_par_date));
            $suivis[$i] = [
                'jour' => $i,
                'n' => $daysToEndOfSales >= $i ? $infoForum : null,
                'n_1' => $infoN1
            ];
        }

        return [
            'suivi' => $suivis,
            'min' => $nombre_par_date[0]['jour'],
            'max' => $i,
            'daysToEndOfSales' => $daysToEndOfSales
        ];
    }

    /**
     * @return mixed[]
     */
    public function obtenirListePourEmargement($id_forum = null): array
    {
        $requete = 'SELECT';
        $requete .= '  i.*, f.societe ';
        $requete .= 'FROM';
        $requete .= '  afup_inscription_forum i ';
        $requete .= 'LEFT JOIN';
        $requete .= '  afup_facturation_forum f ON i.reference = f.reference ';
        $requete .= 'WHERE  i.id_forum =' . $id_forum . ' ';
        $requete .= 'AND    i.type_inscription NOT IN (9, 10, 11, 12, 15) '; // pas orga, conférencier, sponsor, presse
        $requete .= 'ORDER BY i.nom ASC';
        $liste_emargement = [];
        $liste = $this->_bdd->obtenirTous($requete);

        $derniere_lettre = "";
        foreach ($liste as $inscrit) {
            $premiere_lettre = strtoupper($inscrit['nom'][0]);
            if ($derniere_lettre !== $premiere_lettre) {
                $liste_emargement[] = [
                    'nom' => $premiere_lettre,
                    'etat' => -1,
                ];
                $derniere_lettre = $premiere_lettre;
            }
            $liste_emargement[] = $inscrit;
        }

        return $liste_emargement;
    }

    /**
     * @return mixed[]
     */
    public function obtenirListePourEmargementConferencierOrga($id_forum = null): array
    {
        $requete = 'SELECT';
        $requete .= '  i.*, f.societe ';
        $requete .= 'FROM';
        $requete .= '  afup_inscription_forum i ';
        $requete .= 'LEFT JOIN';
        $requete .= '  afup_facturation_forum f ON i.reference = f.reference ';
        $requete .= 'WHERE  i.id_forum =' . $id_forum . ' ';
        $requete .= 'AND    i.type_inscription IN (9, 10, 11, 12, 15) '; // seulement orga, conférencier, sponsor, presse
        $requete .= 'ORDER BY i.nom ASC';
        $liste_emargement = [];
        $liste = $this->_bdd->obtenirTous($requete);

        $derniere_lettre = "";
        foreach ($liste as $inscrit) {
            $premiere_lettre = strtoupper($inscrit['nom'][0]);
            if ($derniere_lettre !== $premiere_lettre) {
                $liste_emargement[] = [
                    'nom' => $premiere_lettre,
                    'etat' => -1,
                ];
                $derniere_lettre = $premiere_lettre;
            }
            $liste_emargement[] = $inscrit;
        }

        return $liste_emargement;
    }

    /**
     * Renvoit la liste des inscriptions au forum
     *
     * @param  string $champs Champs ï¿½ renvoyer
     * @param  string $ordre Tri des enregistrements
     * @param  bool $associatif Renvoyer un tableau associatif ?
     * @return array
     */
    public function obtenirListe($id_forum = null,
                          string $champs = 'i.*',
                          string $ordre = 'i.date',
                          $associatif = false,
                          $filtre = false)
    {
        $requete = 'SELECT
          ' . $champs . ' , 
            
            CASE WHEN i.id_member IS NOT NULL
            THEN ( SELECT MAX(ac.date_fin) AS lastsubcription FROM afup_cotisations ac WHERE ac.type_personne = i.member_type AND ac.id_personne = i.id_member )
            ELSE (SELECT MAX(ac.date_fin) AS lastsubcription
                FROM afup_personnes_physiques app
                LEFT JOIN afup_personnes_morales apm ON apm.id = app.id_personne_morale
                LEFT JOIN afup_cotisations ac ON ac.type_personne = IF(apm.id IS NULL, 0, 1) AND ac.id_personne = IFNULL(apm.id, app.id)
                WHERE app.email = i.email
                GROUP BY app.`id`
                )
            END AS lastsubscription
        FROM
          afup_inscription_forum i 
        LEFT JOIN afup_facturation_forum f ON i.reference = f.reference 
        
        WHERE 1=1 
          AND i.id_forum =' . $id_forum . ' ';
        if ($filtre) {
            $requete .= sprintf('AND CONCAT(i.nom, i.prenom) LIKE %1$s OR f.societe LIKE %1$s ', $this->_bdd->echapper('%' . $filtre . '%'));
        }
        $requete .= 'ORDER BY ' . $ordre;

        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    public function modifierInscription(string $id, $reference, $type_inscription, $civilite, $nom, $prenom,
                                 $email, $telephone, $coupon, $citer_societe, $newsletter_afup,
                                 $newsletter_nexen, $mail_partenaire, $commentaires, $etat, $facturation,
                                 int $transportMode, int $transportDistance)
    {
        $requete = 'UPDATE ';
        $requete .= '  afup_inscription_forum ';
        $requete .= 'SET';
        $requete .= '  reference=' . $this->_bdd->echapper($reference) . ',';
        $requete .= '  type_inscription=' . $this->_bdd->echapper($type_inscription) . ',';
        $requete .= '  montant=' . $GLOBALS['AFUP_Tarifs_Forum'][$type_inscription] . ',';
        $requete .= '  civilite=' . $this->_bdd->echapper($civilite) . ',';
        $requete .= '  nom=' . $this->_bdd->echapper($nom) . ',';
        $requete .= '  prenom=' . $this->_bdd->echapper($prenom) . ',';
        $requete .= '  email=' . $this->_bdd->echapper($email) . ',';
        $requete .= '  telephone=' . $this->_bdd->echapper($telephone) . ',';
        $requete .= '  coupon=' . $this->_bdd->echapper($coupon) . ',';
        $requete .= '  citer_societe=' . $this->_bdd->echapper($citer_societe) . ',';
        $requete .= '  newsletter_afup=' . $this->_bdd->echapper($newsletter_afup) . ',';
        $requete .= '  newsletter_nexen=' . $this->_bdd->echapper($newsletter_nexen) . ',';
        $requete .= '  mail_partenaire=' . $this->_bdd->echapper($mail_partenaire) . ',';
        $requete .= '  commentaires=' . $this->_bdd->echapper($commentaires) . ',';
        $requete .= '  etat=' . $this->_bdd->echapper($etat) . ',';
        $requete .= '  facturation=' . $this->_bdd->echapper($facturation) . ',';
        $requete .= '  transport_mode=' . $this->_bdd->echapper($transportMode) . ',';
        $requete .= '  transport_distance=' . $this->_bdd->echapper($transportDistance);
        $requete .= ' WHERE';
        $requete .= '  id=' . $id;

        $this->modifierEtatInscription($reference, $etat);

        return $this->_bdd->executer($requete);
    }

    public function supprimerInscription(string $id)
    {
        $requete = 'DELETE FROM afup_inscription_forum WHERE id=' . $id;
        return $this->_bdd->executer($requete);
    }

    public function modifierEtatInscription($reference, string $etat)
    {
        $requete = 'UPDATE afup_inscription_forum ';
        $requete .= 'SET etat=' . $etat . ' ';
        $requete .= 'WHERE reference=' . $this->_bdd->echapper($reference);
        $this->_bdd->executer($requete);

        $requete = 'UPDATE afup_facturation_forum ';
        $requete .= 'SET etat=' . $etat . ' ';
        $requete .= 'WHERE reference=' . $this->_bdd->echapper($reference);
        return $this->_bdd->executer($requete);
    }

    public function ajouterRappel($email, $id_forum = null)
    {
        if ($id_forum == null) {
            require_once __DIR__ . '/Forum.php';
            $forum = new Forum($this->_bdd);
            $id_forum = $forum->obtenirDernier();
        }
        $requete = 'INSERT INTO afup_inscriptions_rappels (email, date, id_forum) VALUES (' . $this->_bdd->echapper($email) . ', ' . time() . ', ' . $id_forum . ')';
        return $this->_bdd->executer($requete);
    }

    public function obtenirListeEmailAncienVisiteurs()
    {
        $requete = "SELECT group_concat(DISTINCT email SEPARATOR ';')
                    FROM afup_inscription_forum
                    WHERE `email` <> ''
                    AND right(email, 9) <> '@afup.org'
                    AND type_inscription <> 12
                    AND locate('xxx', email) = 0";
        return $this->_bdd->obtenirUn($requete);
    }
}
