<?php

declare(strict_types=1);

namespace Afup\Site\Forum;

use Afup\Site\Utils\Base_De_Donnees;
use AppBundle\Event\Model\Talk;

class AppelConferencier
{
    const DEFAULT_JOURNEE = 0;

    public function __construct(private readonly Base_De_Donnees $_bdd)
    {
    }

    public function supprimerSession($id)
    {
        $this->delierSession($id);

        $requete = ' DELETE FROM afup_sessions ';
        $requete .= ' WHERE session_id = ' . $this->_bdd->echapper($id);

        return $this->_bdd->executer($requete);
    }

    public function obtenirCommentairesPourSession($id = 0)
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

    public function obtenirConferenciersPourSession($id = 0)
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

    public function obtenirPlanningDeSession($id_session = 0)
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

    public function obtenirSession($id = 0, string $champs = '*', $complement = true)
    {
        $this->_bdd->executer("SET NAMES utf8mb4");

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

    public function obtenirListeSalles($id_forum = null,
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

    public function supprimerSessionDuPlanning($id)
    {
        $requete = ' DELETE FROM afup_forum_planning ';
        $requete .= ' WHERE id = ' . $this->_bdd->echapper($id);

        return $this->_bdd->executer($requete);
    }

    public function modifierSessionDuPlanning($id,
                                       $id_forum,
                                       $id_session,
                                       $debut,
                                       $fin,
                                       $id_salle, $keynote = 0)
    {
        $requete = 'UPDATE afup_forum_planning SET ';
        $requete .= ' id_forum = ' . $this->_bdd->echapper($id_forum) . ', ';
        $requete .= ' id_session = ' . $this->_bdd->echapper($id_session) . ', ';
        $requete .= ' debut = ' . $this->_bdd->echapper($debut) . ', ';
        $requete .= ' fin = ' . $this->_bdd->echapper($fin) . ', ';
        $requete .= ' keynote = ' . $this->_bdd->echapper($keynote) . ', ';
        $requete .= ' id_salle = ' . $this->_bdd->echapper($id_salle) . ' ';
        $requete .= ' WHERE id = ' . (int) $id;
        return $this->_bdd->executer($requete);
    }

    public function ajouterSessionDansPlanning($id_forum,
                                        $id_session,
                                        $debut,
                                        $fin,
                                        $id_salle)
    {
        $donnees = [
            $this->_bdd->echapper($id_forum),
            $this->_bdd->echapper($id_session),
            $this->_bdd->echapper($debut),
            $this->_bdd->echapper($fin),
            $this->_bdd->echapper($id_salle),
        ];

        $requete = ' INSERT INTO afup_forum_planning';
        $requete .= '  (id_forum, id_session, debut, fin, id_salle)';
        $requete .= ' VALUES ';
        $requete .= '  (' . implode(',', $donnees) . ')';

        if ($this->_bdd->executer($requete) === false) {
            return false;
        }
        return $this->_bdd->obtenirUn('SELECT LAST_INSERT_ID()');
    }

    /**
     * @return mixed[]
     */
    public function obtenirListeSessionsAvecResumes($id_forum): array
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

        $sessionsAvecId = [];
        foreach ($sessions as $session) {
            $sessionsAvecId[$session['session_id']] = $session;
        }

        $sessionsAvecResumes = [];


        $forum = new Forum($this->_bdd);
        $forum_details = $forum->obtenir($id_forum);
        if (!$forum_details) {
            return [];
        }
        $directoryPath = __DIR__ . "/../../../htdocs/templates/" . $forum_details['path'] . "/resumes/";
        if (!is_dir($directoryPath)) {
            return [];
        }

        $repertoire = new \DirectoryIterator($directoryPath);
        foreach ($repertoire as $file) {
            if (preg_match("/^[1-9]/", $file->getFilename())) {
                $id = (int) $file->getFilename();
                if (isset($sessionsAvecId[$id])) {
                    $sessionsAvecResumes[$id] = $sessionsAvecId[$id];
                    $sessionsAvecResumes[$id]['file'] = $file->getFilename();
                }
            }
        }

        return $sessionsAvecResumes;
    }

    public function obtenirListeSessionsPlannifies($id_forum)
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

    public function obtenirListeProjets($id_forum = null,
                                 string $champs = 's.*',
                                 string $ordre = 's.date_soumission',
                                 $associatif = false,
                                 $filtre = false,
                                 $only_ids = [])
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
            $requete .= ' AND s.session_id IN (' . implode(', ', $only_ids) . ') ';
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

    public function obtenirListeSessions($id_forum = null,
                                  string $champs = 's.*',
                                  string $ordre = 's.date_soumission',
                                  $associatif = false,
                                  $filtre = false,
                                  $type = 'session',
                                  $needsMentoring = null,
                                  $planned = null,
    ) {
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
            $requete .= ' AND s.needs_mentoring = ' . ((int) $needsMentoring);
        }

        $requete .= ' GROUP BY s.session_id ';
        $requete .= ' ORDER BY ' . $ordre;
        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    public function ajouterConferencier($id_forum, $civilite, $nom, $prenom, $email, $societe, $biographie, $twitter)
    {
        $donnees = [
            $this->_bdd->echapper($id_forum),
            $this->_bdd->echapper($civilite),
            $this->_bdd->echapper($nom),
            $this->_bdd->echapper($prenom),
            $this->_bdd->echapper($email),
            $this->_bdd->echapper($societe),
            $this->_bdd->echapper($biographie),
            $this->_bdd->echapper($twitter),
        ];

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
        int $genre,
        int $plannifie,
        $joindin = null,
        $youtubeId = null,
        $slidesUrl = null,
        $openfeedbackPath = null,
        $blogPostUrl = null,
        $interviewUrl = null,
        $languageCode = null,
        int $skill = null,
        int $needs_mentoring = null,
        $use_markdown = null,
        $video_has_fr_subtitles = null,
        $video_has_en_subtitles = null,
        $date_publication = null,
        $tweets = null,
        $transcript = null,
        $verbatim = null,
    ) {
        $this->_bdd->executer("SET NAMES utf8mb4");

        $requete = 'UPDATE afup_sessions SET ';
        $requete .= ' id_forum = ' . $this->_bdd->echapper($id_forum) . ', ';
        $requete .= ' date_soumission = ' . $this->_bdd->echapper($date_soumission) . ', ';
        $requete .= ' titre = ' . $this->_bdd->echapper($titre) . ', ';
        $requete .= ' abstract = ' . $this->_bdd->echapper($abstract) . ', ';
        $requete .= ' genre = ' . $this->_bdd->echapper($genre) . ', ';
        if (strlen(trim((string) $joindin)) > 0) {
            $requete .= ' joindin = ' . $this->_bdd->echapper($joindin) . ', ';
        } else {
            $requete .= ' joindin = NULL, ';
        }
        if ($youtubeId !== null) {
            $requete .= ' youtube_id = ' . $this->_bdd->echapper($youtubeId) . ', ';
        }
        if ($slidesUrl !== null) {
            $requete .= ' slides_url = ' . $this->_bdd->echapper($slidesUrl) . ', ';
        }
        if ($openfeedbackPath !== null) {
            $requete .= ' openfeedback_path = ' . $this->_bdd->echapper($openfeedbackPath) . ', ';
        }
        if ($blogPostUrl !== null) {
            $requete .= ' blog_post_url = ' . $this->_bdd->echapper($blogPostUrl) . ', ';
        }
        if ($interviewUrl !== null) {
            $requete .= ' interview_url = ' . $this->_bdd->echapper($interviewUrl) . ', ';
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
        if ($video_has_fr_subtitles !== null) {
            $requete .= 'video_has_fr_subtitles = ' . $this->_bdd->echapper($video_has_fr_subtitles) . ', ';
        }
        if ($video_has_en_subtitles !== null) {
            $requete .= 'video_has_en_subtitles = ' . $this->_bdd->echapper($video_has_en_subtitles) . ', ';
        }
        if ($date_publication !== null) {
            $requete .= 'date_publication = ' . $this->_bdd->echapper($date_publication) . ', ';
        }
        if ($tweets !== null) {
            $requete .= 'tweets = ' . $this->_bdd->echapper($tweets) . ', ';
        }
        if ($transcript !== null) {
            $requete .= 'transcript = ' . $this->_bdd->echapper($transcript) . ', ';
        }
        if ($verbatim !== null) {
            $requete .= 'verbatim = ' . $this->_bdd->echapper($verbatim) . ', ';
        }
        $requete .= ' plannifie = ' . $this->_bdd->echapper($plannifie) . ' ';
        $requete .= ' WHERE session_id = ' . (int) $id;

        return $this->_bdd->executer($requete);
    }

    public function modifierJoindinSession($id, $joindin)
    {
        $value = 'NULL';
        if ($joindin) {
            $value = $this->_bdd->echapper($joindin);
        }
        $requete = 'UPDATE afup_sessions SET ';
        $requete .= ' joindin = ' . $value;
        $requete .= ' WHERE session_id = ' . (int) $id;

        return $this->_bdd->executer($requete);
    }

    public function ajouterSession(
        $id_forum,
        $date_soumission,
        $titre,
        $abstract,
        int $genre,
        int $plannifie = 0,
        int $needs_mentoring = 0,
        int $level = Talk::SKILL_NA,
        $useMarkdown = false,
    ) {
        $donnees = [
            $this->_bdd->echapper($id_forum),
            $this->_bdd->echapper($date_soumission),
            $this->_bdd->echapper($titre),
            $this->_bdd->echapper($abstract),
            self::DEFAULT_JOURNEE,
            $this->_bdd->echapper($genre),
            $this->_bdd->echapper($plannifie),
            $this->_bdd->echapper($needs_mentoring),
            $this->_bdd->echapper($level),
            (int) $useMarkdown,
        ];

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

    public function delierSession($session_id)
    {
        $requete = ' DELETE FROM afup_conferenciers_sessions ';
        $requete .= ' WHERE session_id = ' . (int) $session_id;

        return $this->_bdd->executer($requete);
    }

    public function lierConferencierSession($conferencier_id, $session_id)
    {
        if (!$conferencier_id) {
            return true;
        }
        $donnees = [
            $this->_bdd->echapper($conferencier_id),
            $this->_bdd->echapper($session_id),
        ];

        $requete = ' REPLACE INTO afup_conferenciers_sessions';
        $requete .= '  (conferencier_id, session_id) ';
        $requete .= ' VALUES ';
        $requete .= ' (' . implode(',', $donnees) . ')';

        return $this->_bdd->executer($requete);
    }

    public function dejaVote($id_user, $id_session): bool
    {
        $requete = 'SELECT count(*) FROM afup_sessions_vote
        WHERE id_personne_physique=' . $this->_bdd->echapper($id_user)
            . ' AND id_session=' . $this->_bdd->echapper($id_session);

        return (bool) $this->_bdd->obtenirUn($requete);
    }

    public function nbVoteSession($id_session): int
    {
        $requete = 'SELECT count(*) FROM afup_sessions_vote
        WHERE id_session=' . $this->_bdd->echapper($id_session);

        return (int) $this->_bdd->obtenirUn($requete);
    }
}
