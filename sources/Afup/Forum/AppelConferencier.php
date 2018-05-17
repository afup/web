<?php

namespace Afup\Site\Forum;

use Afup\Site\Utils\Configuration;
use Afup\Site\Utils\Mailing;
use AppBundle\Event\Model\Talk;
use Symfony\Component\Translation\Translator;

class AppelConferencier
{
    /**
     * Instance de la couche d'abstraction à la base de données
     * @var     \Afup\Site\Utils\Base_De_Donnees
     * @access  private
     */
    var $_bdd;

    const DEFAULT_JOURNEE = 0;

    /**
     * Constructeur.
     *
     * @param  object $bdd Instance de la couche d'abstraction à la base de données
     * @access public
     * @return void
     */
    public function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    function obtenirSessionsAvecVotePourPersonnePhysique($id_forum, $id_personne_physique)
    {
        $requete = ' SELECT s.* ';
        $requete .= ' FROM afup_sessions s ';
        $requete .= ' INNER JOIN afup_conferenciers_sessions cs ';
        $requete .= ' ON s.session_id = cs.session_id ';
        $requete .= ' INNER JOIN afup_conferenciers c ';
        $requete .= ' ON cs.conferencier_id = c.conferencier_id ';
        $requete .= ' LEFT JOIN afup_sessions_vote v ';
        $requete .= ' ON s.session_id = v.id_session ';
        $requete .= ' WHERE v.id_personne_physique = ' . $this->_bdd->echapper($id_personne_physique);
        $requete .= ' AND c.id_forum = ' . $this->_bdd->echapper($id_forum);

        return $this->_bdd->obtenirAssociatif($requete);
    }

    function obtenirSessionsAvecCommentairePourPersonnePhysique($id_forum, $id_personne_physique)
    {
        $requete = ' SELECT s.* ';
        $requete .= ' FROM afup_sessions s ';
        $requete .= ' INNER JOIN afup_conferenciers_sessions cs ';
        $requete .= ' ON s.session_id = cs.session_id ';
        $requete .= ' INNER JOIN afup_conferenciers c ';
        $requete .= ' ON cs.conferencier_id = c.conferencier_id ';
        $requete .= ' LEFT JOIN afup_forum_sessions_commentaires co ';
        $requete .= ' ON s.session_id = co.id_session ';
        $requete .= ' WHERE co.id_personne_physique = ' . $this->_bdd->echapper($id_personne_physique);
        $requete .= ' AND c.id_forum = ' . $this->_bdd->echapper($id_forum);

        return $this->_bdd->obtenirAssociatif($requete);
    }

    function obtenirSessionSuivanteSansVote($id_forum, $id_personne_physique)
    {
        $sessions_id = array_keys(array(0) + $this->obtenirSessionsAvecVotePourPersonnePhysique($id_forum, $id_personne_physique));

        $requete = ' SELECT s.session_id, ';
        $requete .= ' RAND() as hasard ';
        $requete .= ' FROM afup_sessions s ';
        $requete .= ' INNER JOIN afup_conferenciers_sessions cs ';
        $requete .= ' ON s.session_id = cs.session_id ';
        $requete .= ' INNER JOIN afup_conferenciers c ';
        $requete .= ' ON cs.conferencier_id = c.conferencier_id ';
        $requete .= ' LEFT JOIN afup_sessions_vote v ';
        $requete .= ' ON s.session_id = v.id_session ';
        $requete .= ' WHERE s.session_id NOT IN (' . implode(', ', $sessions_id) . ')';
        $requete .= ' AND c.id_forum = ' . $this->_bdd->echapper($id_forum);
        $requete .= ' ORDER BY hasard';
        $requete .= ' LIMIT 0, 1';

        return $this->_bdd->obtenirUn($requete);
    }

    function obtenirSessionSuivanteSansCommentaire($id_forum, $id_personne_physique)
    {
        $sessions_id = array_keys(array(0) + $this->obtenirSessionsAvecCommentairePourPersonnePhysique($id_forum, $id_personne_physique));

        $requete = ' SELECT s.session_id, ';
        $requete .= ' RAND() as hasard ';
        $requete .= ' FROM afup_sessions s ';
        $requete .= ' INNER JOIN afup_conferenciers_sessions cs ';
        $requete .= ' ON s.session_id = cs.session_id ';
        $requete .= ' INNER JOIN afup_conferenciers c ';
        $requete .= ' ON cs.conferencier_id = c.conferencier_id ';
        $requete .= ' LEFT JOIN afup_forum_sessions_commentaires co ';
        $requete .= ' ON s.session_id = co.id_session ';
        $requete .= ' WHERE s.session_id NOT IN (' . implode(', ', $sessions_id) . ')';
        $requete .= ' AND c.id_forum = ' . $this->_bdd->echapper($id_forum);
        $requete .= ' ORDER BY hasard';
        $requete .= ' LIMIT 0, 1';

        return $this->_bdd->obtenirUn($requete);
    }

    function ajouterCommentaire($id_session,
                                $id_personne_physique,
                                $commentaire,
                                $date,
                                $public)
    {
        $donnees = array(
            $this->_bdd->echapper($id_session),
            $this->_bdd->echapper($id_personne_physique),
            $this->_bdd->echapper($commentaire),
            $this->_bdd->echapper($date),
            $this->_bdd->echapper($public),
        );

        $requete = ' INSERT INTO afup_forum_sessions_commentaires';
        $requete .= '  (id_session, id_personne_physique, commentaire, date, public)';
        $requete .= ' VALUES ';
        $requete .= '  (' . implode(',', $donnees) . ')';

        return $this->_bdd->executer($requete);
    }

    function supprimerConferencier($id)
    {
        $requete = ' DELETE FROM afup_conferenciers ';
        $requete .= ' WHERE conferencier_id = ' . $this->_bdd->echapper($id);

        return $this->_bdd->executer($requete);
    }

    function obtenirConferencier($id = 0, $champs = '*')
    {
        $requete = ' SELECT ';
        $requete .= '  ' . $champs . ' ';
        $requete .= ' FROM ';
        $requete .= '  afup_conferenciers ';
        $requete .= ' WHERE conferencier_id = ' . $this->_bdd->echapper($id);

        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function supprimerSession($id)
    {
        $this->delierSession($id);

        $requete = ' DELETE FROM afup_sessions ';
        $requete .= ' WHERE session_id = ' . $this->_bdd->echapper($id);

        return $this->_bdd->executer($requete);
    }

    function obtenirCommentairesPourSession($id = 0)
    {
        $requete = ' SELECT ';
        $requete .= '  co.*, ';
        $requete .= '  pp.nom, ';
        $requete .= '  pp.prenom ';
        $requete .= ' FROM ';
        $requete .= '  afup_forum_sessions_commentaires co ';
        $requete .= ' LEFT JOIN ';
        $requete .= '  afup_personnes_physiques pp';
        $requete .= ' ON ';
        $requete .= '  co.id_personne_physique = pp.id';
        $requete .= ' WHERE co.id_session = ' . $this->_bdd->echapper($id);
        $requete .= ' ORDER BY date ASC';

        return $this->_bdd->obtenirTous($requete);

    }

    function obtenirConferenciersPourSession($id = 0)
    {
        $requete = ' SELECT ';
        $requete .= '  LOWER(CONCAT(SUBSTRING(c.prenom, 1, 1), c.nom)) as code, ';
        $requete .= '  c.* ';
        $requete .= ' FROM ';
        $requete .= '  afup_conferenciers c ';
        $requete .= ' INNER JOIN ';
        $requete .= '  afup_conferenciers_sessions cs';
        $requete .= ' ON ';
        $requete .= '  c.conferencier_id = cs.conferencier_id';
        $requete .= ' WHERE cs.session_id = ' . $this->_bdd->echapper($id);

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirPlanningDeSession($id_session = 0)
    {
        $requete = ' SELECT ';
        $requete .= '  se.*, ';
        $requete .= '  sa.nom as nom_salle, ';
        $requete .= '  pl.id, ';
        $requete .= '  pl.debut, ';
        $requete .= '  pl.fin, ';
        $requete .= '  pl.keynote, ';
        $requete .= '  pl.id_salle ';
        $requete .= ' FROM ';
        $requete .= '  afup_sessions se ';
        $requete .= ' LEFT JOIN ';
        $requete .= '  afup_forum_planning pl ';
        $requete .= ' ON se.session_id = pl.id_session';
        $requete .= ' LEFT JOIN ';
        $requete .= '  afup_forum_salle sa ';
        $requete .= ' ON sa.id = pl.id_salle';
        $requete .= ' WHERE se.session_id = ' . $this->_bdd->echapper($id_session);

        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function obtenirSession($id = 0, $champs = '*', $complement = true)
    {
        $requete = ' SELECT ';
        $requete .= '  ' . $champs . ' ';
        $requete .= ' FROM ';
        $requete .= '  afup_sessions ';
        $requete .= ' WHERE session_id = ' . $this->_bdd->echapper($id);

        $session = $this->_bdd->obtenirEnregistrement($requete);

        if ($complement) {
            $requete = ' SELECT ';
            $requete .= '  cs.conferencier_id, ';
            $requete .= '  c.id_forum';
            $requete .= ' FROM ';
            $requete .= '  afup_conferenciers_sessions cs';
            $requete .= ' INNER JOIN ';
            $requete .= '  afup_conferenciers c';
            $requete .= ' ON ';
            $requete .= '  c.conferencier_id = cs.conferencier_id';
            $requete .= ' WHERE cs.session_id = ' . $this->_bdd->echapper($id);

            $complements = $this->_bdd->obtenirTous($requete);
            $i = 1;
            foreach ($complements as $complement) {
                $session['id_forum'] = $complement['id_forum'];
                $session['conferencier_id_' . $i] = $complement['conferencier_id'];
                $i++;
            }
        }

        return $session;
    }

    function obtenirListeSalles($id_forum = null,
                                $associatif = false)
    {
        $requete = ' SELECT ';
        $requete .= '  id, nom ';
        $requete .= ' FROM afup_forum_salle sa ';
        $requete .= ' WHERE sa.id_forum = ' . $this->_bdd->echapper($id_forum);

        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }

    }

    function obtenirListeCommentaires($id_forum = null,
                                      $champs = 'co.*',
                                      $ordre = 'co.nom',
                                      $associatif = false,
                                      $extra = '')
    {
        $requete = ' SELECT ';
        $requete .= '  ' . $champs . ' ';
        $requete .= ' FROM afup_forum_sessions_commentaires co ';
        $requete .= ' INNER JOIN afup_conferenciers_sessions cs ';
        $requete .= ' ON cs.session_id = co.id_session ';
        $requete .= ' INNER JOIN afup_conferenciers c ';
        $requete .= ' ON c.conferencier_id = cs.conferencier_id ';
        $requete .= ' WHERE c.id_forum = ' . $this->_bdd->echapper($id_forum);
        $requete .= $extra;
        $requete .= ' ORDER BY ' . $ordre;

        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    function obtenirListeConferenciers($id_forum = null,
                                       $champs = 'c.*',
                                       $ordre = 'c.nom',
                                       $associatif = false,
                                       $filtre = false)
    {
        $requete = ' SELECT ';
        $requete .= '  ' . $champs . ' ';
        $requete .= ' FROM ';
        $requete .= '  afup_conferenciers c ';
        $requete .= ' WHERE c.id_forum = ' . $this->_bdd->echapper($id_forum);
        if ($filtre) {
            $requete .= '  c.nom LIKE \'%' . $filtre . '%\' ';
        }
        $requete .= ' ORDER BY ' . $ordre;

        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    function obtenirNbConferenciersDistinct($id_forum = null,
                                            $champs = 'c.*',
                                            $ordre = 'c.nom',
                                            $associatif = false,
                                            $filtre = false)
    {
        $requete = ' SELECT count(*) FROM (';
        $requete .= ' SELECT nom, prenom';
        $requete .= ' FROM ';
        $requete .= '  afup_conferenciers ';
        $requete .= ' WHERE id_forum = ' . $this->_bdd->echapper($id_forum);
        $requete .= ' GROUP BY nom, prenom';
        $requete .= ' ) c';

        return $this->_bdd->obtenirUn($requete);
    }

    function supprimerSessionDuPlanning($id)
    {
        $requete = ' DELETE FROM afup_forum_planning ';
        $requete .= ' WHERE id = ' . $this->_bdd->echapper($id);

        return $this->_bdd->executer($requete);
    }

    function modifierSessionDuPlanning($id,
                                       $id_forum,
                                       $id_session,
                                       $debut,
                                       $fin,
                                       $id_salle, $keynote)
    {
        $requete = 'UPDATE afup_forum_planning SET ';
        $requete .= ' id_forum = ' . $this->_bdd->echapper($id_forum) . ', ';
        $requete .= ' id_session = ' . $this->_bdd->echapper($id_session) . ', ';
        $requete .= ' debut = ' . $this->_bdd->echapper($debut) . ', ';
        $requete .= ' fin = ' . $this->_bdd->echapper($fin) . ', ';
        $requete .= ' keynote = ' . $this->_bdd->echapper($keynote) . ', ';
        $requete .= ' id_salle = ' . $this->_bdd->echapper($id_salle) . ' ';
        $requete .= ' WHERE id = ' . (int)$id;
        return $this->_bdd->executer($requete);
    }

    function ajouterSessionDansPlanning($id_forum,
                                        $id_session,
                                        $debut,
                                        $fin,
                                        $id_salle)
    {
        $donnees = array(
            $this->_bdd->echapper($id_forum),
            $this->_bdd->echapper($id_session),
            $this->_bdd->echapper($debut),
            $this->_bdd->echapper($fin),
            $this->_bdd->echapper($id_salle),
        );

        $requete = ' INSERT INTO afup_forum_planning';
        $requete .= '  (id_forum, id_session, debut, fin, id_salle)';
        $requete .= ' VALUES ';
        $requete .= '  (' . implode(',', $donnees) . ')';

        if ($this->_bdd->executer($requete) === false) {
            return false;
        }
        return $this->_bdd->obtenirUn('SELECT LAST_INSERT_ID()');
    }

    function obtenirListeSessionsAvecResumes($id_forum)
    {
        $requete = ' SELECT ';
        $requete .= '  se.session_id, ';
        $requete .= '  se.* ';
        $requete .= ' FROM ';
        $requete .= '  afup_sessions se ';
        $requete .= ' LEFT JOIN ';
        $requete .= '  afup_forum_planning pl ';
        $requete .= ' ON se.session_id = pl.id_session';
        $requete .= ' WHERE se.id_forum = ' . $this->_bdd->echapper($id_forum);
        $requete .= ' AND se.plannifie = 1';
        $requete .= ' AND se.genre != 9 ';
        $requete .= ' ORDER BY ';
        $requete .= '  pl.debut, se.titre';

        $sessions = $this->_bdd->obtenirTous($requete);

        $sessionsAvecId = array();
        foreach ($sessions as $session) {
            $sessionsAvecId[$session['session_id']] = $session;
        }

        $sessionsAvecResumes = array();


        $forum = new Forum($this->_bdd);
        $forum_details = $forum->obtenir($id_forum);

        $repertoire = new \DirectoryIterator(dirname(__FILE__) . "/../../htdocs/templates/" . $forum_details['path'] . "/resumes/");
        foreach ($repertoire as $file) {
            if (preg_match("/^[1-9]/", $file->getFilename())) {
                $id = (int)$file->getFilename();
                if (isset($sessionsAvecId[$id])) {
                    $sessionsAvecResumes[$id] = $sessionsAvecId[$id];
                    $sessionsAvecResumes[$id]['file'] = $file->getFilename();
                }
            }
        }

        return $sessionsAvecResumes;
    }

    function obtenirListeSessionsPourConferencier($id_forum, $id_conferencier)
    {
        $requete = ' SELECT ';
        $requete .= '  se.session_id, ';
        $requete .= '  se.* ';
        $requete .= ' FROM ';
        $requete .= '  afup_sessions se ';
        $requete .= ' INNER JOIN afup_conferenciers_sessions cs ';
        $requete .= '  ON cs.session_id = se.session_id ';
        $requete .= ' WHERE se.id_forum = ' . $this->_bdd->echapper($id_forum);
        $requete .= ' AND cs.conferencier_id = ' . $this->_bdd->echapper($id_conferencier);
        $requete .= ' AND se.genre != 9 ';
        $requete .= ' ORDER BY ';
        $requete .= '  se.titre';

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirListeSessionsPlannifies($id_forum)
    {
        $requete = ' SELECT ';
        $requete .= " ( SELECT CONCAT(c.prenom, ' ', c.nom,' - ', c.societe )  FROM afup_conferenciers_sessions cs INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id WHERE cs.session_id = se.session_id order by c.conferencier_id asc limit 1) as conf1 ,
                      ( SELECT twitter  FROM afup_conferenciers_sessions cs INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id WHERE cs.session_id = se.session_id order by c.conferencier_id asc limit 1) as twitter1 ,
                      ( SELECT cs.conferencier_id  FROM afup_conferenciers_sessions cs INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id WHERE cs.session_id = se.session_id order by c.conferencier_id asc limit 1) as conferencier_id1 ,
                      ( SELECT CONCAT(c.prenom, ' ', c.nom,' - ', c.societe)  FROM afup_conferenciers_sessions cs INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id WHERE cs.session_id = se.session_id order by c.conferencier_id asc limit 1,1) as conf2 ,
                      ( SELECT twitter  FROM afup_conferenciers_sessions cs INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id WHERE cs.session_id = se.session_id order by c.conferencier_id asc limit 1,1) as twitter2 ,
                      ( SELECT cs.conferencier_id  FROM afup_conferenciers_sessions cs INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id WHERE cs.session_id = se.session_id order by c.conferencier_id asc limit 1,1) as conferencier_id2 , ";

        $requete .= '  se.*, ';
        $requete .= '  IF(se.journee = 1, "boss", IF(se.journee = 2, "geek", "boss geek")) as journee, ';
        $requete .= '  sa.nom as nom_salle, ';
        $requete .= '  pl.id, ';
        $requete .= '  pl.debut, ';
        $requete .= '  pl.fin, ';
        $requete .= '  pl.keynote, ';
        $requete .= '  pl.id_salle, ';
        $requete .= '  se.joindin ';
        $requete .= ' FROM ';
        $requete .= '  afup_sessions se ';
        $requete .= ' LEFT JOIN ';
        $requete .= '  afup_forum_planning pl ';
        $requete .= ' ON se.session_id = pl.id_session';
        $requete .= ' LEFT JOIN ';
        $requete .= '  afup_forum_salle sa ';
        $requete .= ' ON sa.id = pl.id_salle';
        $requete .= ' WHERE se.id_forum = ' . $this->_bdd->echapper($id_forum);
        $requete .= ' AND se.genre != 9 ';
        $requete .= ' AND se.plannifie = 1';
        $requete .= ' ORDER BY ';
        $requete .= '  pl.debut, sa.nom ,se.titre';

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirListeSessionsNotees($id_forum = null)
    {
        $requete = ' SELECT ';
        $requete .= " ( SELECT CONCAT(c.prenom, ' ', c.nom,' - ', c.societe)  FROM afup_conferenciers_sessions cs INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id WHERE cs.session_id = s.session_id order by c.conferencier_id asc limit 1) as conf1 ,
                      ( SELECT CONCAT(c.prenom, ' ', c.nom,' - ', c.societe)  FROM afup_conferenciers_sessions cs INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id WHERE cs.session_id = s.session_id order by c.conferencier_id asc limit 1,1) as conf2 , ";
        $requete .= '  SUM(no.note) as note, ';
        $requete .= '  s.* ';
        $requete .= ' FROM ';
        $requete .= '  afup_sessions s ';
        $requete .= ' INNER JOIN afup_sessions_note no ';
        $requete .= '  ON s.session_id = no.session_id ';
        $requete .= ' WHERE s.id_forum = ' . $this->_bdd->echapper($id_forum);
        $requete .= ' GROUP BY no.session_id ';
        $requete .= ' ORDER BY note DESC';


        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirListeProjets($id_forum = null,
                                 $champs = 's.*',
                                 $ordre = 's.date_soumission',
                                 $associatif = false,
                                 $filtre = false,
                                 $only_ids = array())
    {
        $requete = ' SELECT ';
        $requete .= '  COUNT(co.id) as commentaires_nombre, ';
        $requete .= '  ' . $champs . ' ';
        $requete .= ' FROM ';
        $requete .= '  afup_sessions s ';
        $requete .= ' INNER JOIN afup_conferenciers_sessions cs ';
        $requete .= '  ON cs.session_id = s.session_id ';
        $requete .= ' INNER JOIN afup_conferenciers c ';
        $requete .= '  ON c.conferencier_id = cs.conferencier_id ';
        $requete .= ' LEFT JOIN afup_forum_sessions_commentaires co ';
        $requete .= '  ON cs.session_id = co.id_session ';
        $requete .= ' WHERE c.id_forum = ' . $this->_bdd->echapper($id_forum);
        if ($filtre) {
            $requete .= ' AND s.titre LIKE \'%' . $filtre . '%\' ';
        }
        if ($only_ids) {
            $requete .= ' AND s.session_id IN (' . implode($only_ids, ', ') . ') ';
        }
        $requete .= ' AND s.genre = 9 ';
        $requete .= ' GROUP BY s.session_id ';
        $requete .= ' ORDER BY ' . $ordre;

        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    function obtenirListeProjetsPlannifies($id_forum = null,
                                           $champs = 's.*',
                                           $ordre = 's.date_soumission',
                                           $associatif = false)
    {
        $requete = ' SELECT ';
        $requete .= '  COUNT(co.id) as commentaires_nombre, ';
        $requete .= '  ' . $champs . ' ';
        $requete .= ' FROM ';
        $requete .= '  afup_sessions s ';
        $requete .= ' INNER JOIN afup_conferenciers_sessions cs ';
        $requete .= '  ON cs.session_id = s.session_id ';
        $requete .= ' INNER JOIN afup_conferenciers c ';
        $requete .= '  ON c.conferencier_id = cs.conferencier_id ';
        $requete .= ' LEFT JOIN afup_forum_sessions_commentaires co ';
        $requete .= '  ON cs.session_id = co.id_session ';
        $requete .= ' WHERE c.id_forum = ' . $this->_bdd->echapper($id_forum);
        $requete .= ' AND s.plannifie = 1 ';
        $requete .= ' AND s.genre = 9 ';
        $requete .= ' GROUP BY s.session_id ';
        $requete .= ' ORDER BY ' . $ordre;

        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    function obtenirListeSessions($id_forum = null,
                                  $champs = 's.*',
                                  $ordre = 's.date_soumission',
                                  $associatif = false,
                                  $filtre = false,
                                  $type = 'session',
                                  $needsMentoring = null,
                                  $planned = null
    )
    {
        $requete = ' SELECT ';
        $requete .= '  COUNT(co.id) as commentaires_nombre, ';
        $requete .= ' (SELECT AVG(vote) FROM afup_sessions_vote_github asvg WHERE asvg.session_id = s.session_id) AS note, ';
        $requete .= ' (SELECT COUNT(vote) FROM afup_sessions_vote_github asvg WHERE asvg.session_id = s.session_id) AS nb_vote_visiteur, ';
        $requete .= '  ' . $champs . ' ';
        $requete .= ' FROM ';
        $requete .= '  afup_sessions s ';
        $requete .= ' LEFT JOIN afup_forum_sessions_commentaires co ';
        $requete .= '  ON s.session_id = co.id_session ';

        if (null !== $planned) {
            $requete .= ' JOIN afup_forum_planning ON (s.session_id = afup_forum_planning.id_session) ';
        }

        $requete .= ' WHERE s.id_forum = ' . $this->_bdd->echapper($id_forum);
        if ($filtre) {
            $requete .= ' AND s.titre LIKE \'%' . $filtre . '%\' ';
        }
        switch ($type) {
            case 'session':
                $requete .= ' AND s.genre < 9 ';
                break;
            case 'projet':
                $requete .= ' AND s.genre = 9 ';
                break;

            default:
                ;
                break;
        }

        if (null !== $needsMentoring) {
            $requete .= ' AND s.needs_mentoring = ' . ((int)$needsMentoring);
        }

        $requete .= ' GROUP BY s.session_id ';
        $requete .= ' ORDER BY ' . $ordre;
        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    function modifierConferencier($id,
                                  $id_forum,
                                  $civilite,
                                  $nom,
                                  $prenom,
                                  $email,
                                  $societe,
                                  $biographie,
                                  $twitter = null)
    {
        $requete = 'UPDATE afup_conferenciers SET ';
        $requete .= ' id_forum = ' . $this->_bdd->echapper($id_forum) . ', ';
        $requete .= ' civilite = ' . $this->_bdd->echapper($civilite) . ', ';
        $requete .= ' nom = ' . $this->_bdd->echapper($nom) . ', ';
        $requete .= ' prenom = ' . $this->_bdd->echapper($prenom) . ', ';
        $requete .= ' email = ' . $this->_bdd->echapper($email) . ', ';
        $requete .= ' societe = ' . $this->_bdd->echapper($societe) . ', ';
        if ($twitter !== null) {
            $requete .= ' twitter = ' . $this->_bdd->echapper($twitter) . ', ';
        }
        $requete .= ' biographie = ' . $this->_bdd->echapper($biographie) . ' ';
        $requete .= ' WHERE conferencier_id = ' . (int)$id;

        return $this->_bdd->executer($requete);
    }

    function ajouterConferencier($id_forum, $civilite, $nom, $prenom, $email, $societe, $biographie, $twitter)
    {
        $donnees = array(
            $this->_bdd->echapper($id_forum),
            $this->_bdd->echapper($civilite),
            $this->_bdd->echapper($nom),
            $this->_bdd->echapper($prenom),
            $this->_bdd->echapper($email),
            $this->_bdd->echapper($societe),
            $this->_bdd->echapper($biographie),
            $this->_bdd->echapper($twitter),
        );

        $requete = ' INSERT INTO afup_conferenciers';
        $requete .= '  (id_forum, civilite, nom, prenom, email, societe, biographie, twitter)';
        $requete .= ' VALUES ';
        $requete .= '  (' . implode(',', $donnees) . ')';

        if ($this->_bdd->executer($requete) === false) {
            return false;
        }
        return $this->_bdd->obtenirUn('select LAST_INSERT_ID()');
    }

    public function modifierSession(
        $id,
        $id_forum,
        $date_soumission,
        $titre,
        $abstract,
        $genre,
        $plannifie,
        $joindin = null,
        $youtubeId = null,
        $slidesUrl = null,
        $blogPostUrl = null,
        $languageCode = null,
        $skill = null,
        $needs_mentoring = null,
        $use_markdown = null
    ) {
        $requete = 'UPDATE afup_sessions SET ';
        $requete .= ' id_forum = ' . $this->_bdd->echapper($id_forum) . ', ';
        $requete .= ' date_soumission = ' . $this->_bdd->echapper($date_soumission) . ', ';
        $requete .= ' titre = ' . $this->_bdd->echapper($titre) . ', ';
        $requete .= ' abstract = ' . $this->_bdd->echapper($abstract) . ', ';
        $requete .= ' genre = ' . $this->_bdd->echapper($genre) . ', ';
        if ($joindin !== null) {
            $requete .= ' joindin = ' . $this->_bdd->echapper($joindin) . ', ';
        }
        if ($youtubeId !== null) {
            $requete .= ' youtube_id = ' . $this->_bdd->echapper($youtubeId) . ', ';
        }
        if ($slidesUrl !== null) {
            $requete .= ' slides_url = ' . $this->_bdd->echapper($slidesUrl) . ', ';
        }
        if ($blogPostUrl !== null) {
            $requete .= ' blog_post_url = ' . $this->_bdd->echapper($blogPostUrl) . ', ';
        }
        if ($languageCode !== null) {
            $requete .= ' language_code = ' . $this->_bdd->echapper($languageCode) . ', ';
        }
        if ($skill !== null) {
            $requete .= 'skill = ' . $this->_bdd->echapper($skill) . ', ';
        }
        if ($needs_mentoring !== null) {
            $requete .= 'needs_mentoring = ' . $this->_bdd->echapper($needs_mentoring) . ', ';
        }
        if ($use_markdown !== null) {
            $requete .= 'markdown = ' . $this->_bdd->echapper($use_markdown) . ', ';
        }
        $requete .= ' plannifie = ' . $this->_bdd->echapper($plannifie) . ' ';
        $requete .= ' WHERE session_id = ' . (int)$id;

        return $this->_bdd->executer($requete);
    }

    public function modifierJoindinSession($id, $joindin)
    {
        $requete = 'UPDATE afup_sessions SET ';
        $requete .= ' joindin = ' . $this->_bdd->echapper($joindin);
        $requete .= ' WHERE session_id = ' . (int)$id;

        return $this->_bdd->executer($requete);
    }

    public function ajouterSession(
        $id_forum,
        $date_soumission,
        $titre,
        $abstract,
        $genre,
        $plannifie = 0,
        $needs_mentoring = 0,
        $level = Talk::SKILL_NA,
        $useMarkdown = false
    ) {

        $donnees = array(
            $this->_bdd->echapper($id_forum),
            $this->_bdd->echapper($date_soumission),
            $this->_bdd->echapper($titre),
            $this->_bdd->echapper($abstract),
            self::DEFAULT_JOURNEE,
            $this->_bdd->echapper($genre),
            $this->_bdd->echapper($plannifie),
            $this->_bdd->echapper($needs_mentoring),
            $this->_bdd->echapper($level),
            (int)$useMarkdown
        );

        $requete = ' INSERT INTO afup_sessions
          (id_forum, date_soumission, titre, abstract, journee, genre, plannifie, needs_mentoring, skill, markdown)
         VALUES 
          (' . implode(',', $donnees) . ')';

        $res = $this->_bdd->executer($requete);
        if ($res === false) {
            return false;
        }
        return $this->_bdd->obtenirUn('select LAST_INSERT_ID()');
    }

    function delierSession($session_id)
    {
        $requete = ' DELETE FROM afup_conferenciers_sessions ';
        $requete .= ' WHERE session_id = ' . (int)$session_id;

        return $this->_bdd->executer($requete);
    }

    public function lierConferencierSession($conferencier_id, $session_id)
    {
        $donnees = array(
            $this->_bdd->echapper($conferencier_id),
            $this->_bdd->echapper($session_id),
        );

        $requete = ' REPLACE INTO afup_conferenciers_sessions';
        $requete .= '  (conferencier_id, session_id) ';
        $requete .= ' VALUES ';
        $requete .= ' (' . implode(',', $donnees) . ')';

        return $this->_bdd->executer($requete);
    }

    /**
     * Envoi un email de confirmation au conférencier et mets en copie le bureau
     * @param int $session_id
     * @param Translator $translator
     * @return bool
     */
    public function envoyerEmail($session_id, Translator $translator = null)
    {
        $configuration = new Configuration(dirname(__FILE__) . '/../../../configs/application/config.php');

        $requete = 'SELECT prenom, nom, email, af.titre, a_s.titre AS conf_title, a_s.abstract
                    FROM afup_conferenciers ac
                    INNER JOIN afup_conferenciers_sessions acs
                        ON ac.conferencier_id = acs.conferencier_id
                    INNER JOIN afup_sessions a_s
                        ON a_s.session_id = acs.session_id
                    INNER JOIN afup_forum af
                        ON af.id = a_s.id_forum
                    WHERE
                        acs.session_id=' . $this->_bdd->echapper($session_id);

        $conferenciers = $this->_bdd->obtenirTous($requete);
        $conf = current($conferenciers);

        $corps = $translator->trans('Bonjour,') .'

' . $translator->trans('Nous avons bien enregistré votre soumission pour notre évènement') . ' (' . $conf['titre'] . ')
' . $translator->trans('Vous recevrez une réponse prochainement.') . '

' . $translator->trans('Vous avez soumis le sujet suivant :') . ' ' . $conf['conf_title'] . '
' . $conf['abstract'] . '

' . $translator->trans('Le bureau');

        $ok = false;
        foreach ($conferenciers as $personne) {
            $ok = Mailing::envoyerMail(
                $configuration->obtenir('mails|email_expediteur'),
                array($personne['email'], $personne['nom'] . ' ' . $personne['prenom']),
                $translator->trans('Votre proposition pour:') . $personne['titre'] . "\n",
                $corps);
        }
        return $ok;
    }

    /**
     * La note est exprimée de la façon suivante
     *
     * PO = Plutot oui
     * PN = Plutot non
     * O = oui
     * N = non
     */
    function noterLaSession($session_id, $note, $salt, $date)
    {
        $donnees = array($this->_bdd->echapper($session_id),
            $this->_bdd->echapper($note),
            $this->_bdd->echapper($salt),
            $this->_bdd->echapper($date));

        $requete = 'INSERT INTO afup_sessions_note (session_id, note, salt, date_soumission)
            VALUES (' . implode(',', $donnees) . ')';

        return $this->_bdd->executer($requete);
    }

    function obtenirGrainDeSel($user_id)
    {
        list($usec, $sec) = explode(" ", microtime());
        return md5($user_id . ((float)$usec + (float)$sec));
    }

    function envoyerResumeVote($salt, $user_id)
    {
        $requete = 'SELECT';
        $requete .= '  nom, prenom, email ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE';
        $requete .= '  id=' . $this->_bdd->echapper($user_id);

        $resultat = $this->_bdd->obtenirEnregistrement($requete);

        /**
         * @var $configuration Configuration
         */
        $configuration = $GLOBALS['AFUP_CONF'];

        $requete = 'SELECT titre, note
            FROM afup_sessions_note INNER JOIN afup_sessions ON
            afup_sessions_note.session_id=afup_sessions.session_id
            WHERE salt=' . $this->_bdd->echapper($salt);

        $resultat = $this->_bdd->obtenirEnregistrement($requete);

        $sujet = "Vos votes de session\n";

        $corps = "Bonjour, \n\n";
        $corps .= "Nous avons bien enregistré votre vote sur les sessions du forum.\n\n";
        $corps .= $resultat['titre'] . ' ' . $resultat['note'] . "\n";
        $corps .= "le grain de sel pour retrouver l'enregistrement dans la base est $salt";
        $corps .= "\nLe bureau\n\n";
        $corps .= $configuration->obtenir('afup|raison_sociale') . "\n";
        $corps .= $configuration->obtenir('afup|adresse') . "\n";
        $corps .= $configuration->obtenir('afup|code_postal') . " " . $configuration->obtenir('afup|ville') . "\n";

        $ok = Mailing::envoyerMail(
            $configuration->obtenir('mails|email_expediteur'),
            array($resultat['email'], $resultat['nom'] . ' ' . $resultat['prenom']),
            $sujet,
            $corps);

        return $ok;
    }

    function aVote($id_user, $id_session)
    {
        $donnees = array(
            $this->_bdd->echapper($id_user),
            $this->_bdd->echapper($id_session),
            1
        );

        $requete = 'INSERT INTO afup_sessions_vote (id_personne_physique,
        id_session, a_vote) VALUES (' . implode(',', $donnees) . ')';

        return $this->_bdd->executer($requete);
    }

    function dejaVote($id_user, $id_session)
    {
        $requete = 'SELECT count(*) FROM afup_sessions_vote
        WHERE id_personne_physique=' . $this->_bdd->echapper($id_user)
            . ' AND id_session=' . $this->_bdd->echapper($id_session);

        return (bool)$this->_bdd->obtenirUn($requete);
    }

    function nbVoteSession($id_session)
    {
        $requete = 'SELECT count(*) FROM afup_sessions_vote
        WHERE id_session=' . $this->_bdd->echapper($id_session);

        return (int)$this->_bdd->obtenirUn($requete);
    }

    public function obtenirListeEmailAncienConferencier()
    {
        $requete = "SELECT group_concat(DISTINCT c.email SEPARATOR ';')
                    FROM afup_conferenciers c
                    INNER JOIN afup_conferenciers_sessions cs ON c.conferencier_id = cs.conferencier_id
                    INNER JOIN afup_sessions s ON cs.session_id=s.session_id
                    WHERE s.plannifie = 1";
        return $this->_bdd->obtenirUn($requete);
    }
}
