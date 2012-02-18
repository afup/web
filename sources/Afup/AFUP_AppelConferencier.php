<?php

class AFUP_AppelConferencier
{
    /**
     * Instance de la couche d'abstraction à la base de données
     * @var     object
     * @access  private
     */
    var $_bdd;

    /**
     * Constructeur.
     *
     * @param  object    $bdd   Instance de la couche d'abstraction à la base de données
     * @access public
     * @return void
     */
    public function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    function obtenirSessionsAvecVotePourPersonnePhysique($id_forum, $id_personne_physique)
    {
        $requete  = ' SELECT s.* ';
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
        $requete  = ' SELECT s.* ';
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

        $requete  = ' SELECT s.session_id, ';
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

        $requete  = ' SELECT s.session_id, ';
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

        $requete  = ' INSERT INTO afup_forum_sessions_commentaires';
        $requete .= '  (id_session, id_personne_physique, commentaire, date, public)';
        $requete .= ' VALUES ';
        $requete .= '  (' . implode(',', $donnees) . ')';

        return $this->_bdd->executer($requete);
    }

    function supprimerConferencier($id)
    {
        $requete  = ' DELETE FROM afup_conferenciers ';
        $requete .= ' WHERE conferencier_id = '.$this->_bdd->echapper($id);

        return $this->_bdd->executer($requete);
    }

    function obtenirConferencier($id = 0, $champs = '*')
    {
        $requete  = ' SELECT ';
        $requete .= '  '. $champs .' ';
        $requete .= ' FROM ';
        $requete .= '  afup_conferenciers ';
        $requete .= ' WHERE conferencier_id = '.$this->_bdd->echapper($id);

        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function supprimerSession($id)
    {
        $this->delierSession($id);

        $requete  = ' DELETE FROM afup_sessions ';
        $requete .= ' WHERE session_id = '.$this->_bdd->echapper($id);

        return $this->_bdd->executer($requete);
    }

    function obtenirCommentairesPourSession($id = 0)
    {
        $requete  = ' SELECT ';
        $requete .= '  co.*, ';
        $requete .= '  pp.nom, ';
        $requete .= '  pp.prenom ';
        $requete .= ' FROM ';
        $requete .= '  afup_forum_sessions_commentaires co ';
        $requete .= ' LEFT JOIN ';
        $requete .= '  afup_personnes_physiques pp';
        $requete .= ' ON ';
        $requete .= '  co.id_personne_physique = pp.id';
        $requete .= ' WHERE co.id_session = '.$this->_bdd->echapper($id);
        $requete .= ' ORDER BY date ASC';

        return $this->_bdd->obtenirTous($requete);

    }

    function obtenirConferenciersPourSession($id = 0)
    {
        $requete  = ' SELECT ';
        $requete .= '  LOWER(CONCAT(SUBSTRING(c.prenom, 1, 1), c.nom)) as code, ';
        $requete .= '  c.* ';
        $requete .= ' FROM ';
        $requete .= '  afup_conferenciers c ';
        $requete .= ' INNER JOIN ';
        $requete .= '  afup_conferenciers_sessions cs';
        $requete .= ' ON ';
        $requete .= '  c.conferencier_id = cs.conferencier_id';
        $requete .= ' WHERE cs.session_id = '.$this->_bdd->echapper($id);

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirPlanningDeSession($id_session = 0) {
        $requete  = ' SELECT ';
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
        $requete .= ' WHERE se.session_id = '.$this->_bdd->echapper($id_session);

        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function obtenirSession($id = 0, $champs = '*', $complement = true)
    {
        $requete  = ' SELECT ';
        $requete .= '  '. $champs .' ';
        $requete .= ' FROM ';
        $requete .= '  afup_sessions ';
        $requete .= ' WHERE session_id = '.$this->_bdd->echapper($id);

        $session = $this->_bdd->obtenirEnregistrement($requete);

        if ($complement) {
            $requete  = ' SELECT ';
            $requete .= '  cs.conferencier_id, ';
            $requete .= '  c.id_forum';
            $requete .= ' FROM ';
            $requete .= '  afup_conferenciers_sessions cs';
            $requete .= ' INNER JOIN ';
            $requete .= '  afup_conferenciers c';
            $requete .= ' ON ';
            $requete .= '  c.conferencier_id = cs.conferencier_id';
            $requete .= ' WHERE cs.session_id = '.$this->_bdd->echapper($id);

            $complements = $this->_bdd->obtenirTous($requete);
            $i = 1;
            foreach ($complements as $complement) {
                $session['id_forum'] = $complement['id_forum'];
                $session['conferencier_id_'.$i] = $complement['conferencier_id'];
                $i++;
            }
        }

        return $session;
    }

    function obtenirListeSalles($id_forum = null,
                                $associatif = false) {
        $requete  = ' SELECT ';
        $requete .= '  id, nom ';
        $requete .= ' FROM afup_forum_salle sa ';
        $requete .= ' WHERE sa.id_forum = '.$this->_bdd->echapper($id_forum);

        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }

    }

    function obtenirListeCommentaires($id_forum   = null,
                          $champs     = 'co.*',
                          $ordre      = 'co.nom',
                          $associatif = false,
                          $extra      = '')
    {
        $requete  = ' SELECT ';
        $requete .= '  ' . $champs . ' ';
        $requete .= ' FROM afup_forum_sessions_commentaires co ';
        $requete .= ' INNER JOIN afup_conferenciers_sessions cs ';
        $requete .= ' ON cs.session_id = co.id_session ';
        $requete .= ' INNER JOIN afup_conferenciers c ';
        $requete .= ' ON c.conferencier_id = cs.conferencier_id ';
        $requete .= ' WHERE c.id_forum = '.$this->_bdd->echapper($id_forum);
        $requete .= $extra;
        $requete .= ' ORDER BY ' . $ordre;

        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    function obtenirListeConferenciers($id_forum   = null,
                          $champs     = 'c.*',
                          $ordre      = 'c.nom',
                          $associatif = false,
                          $filtre     = false)
    {
        $requete  = ' SELECT ';
        $requete .= '  ' . $champs . ' ';
        $requete .= ' FROM ';
        $requete .= '  afup_conferenciers c ';
        $requete .= ' WHERE c.id_forum = '.$this->_bdd->echapper($id_forum);
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

    function supprimerSessionDuPlanning($id)
    {
        $requete  = ' DELETE FROM afup_forum_planning ';
        $requete .= ' WHERE id = '.$this->_bdd->echapper($id);

        return $this->_bdd->executer($requete);
    }

    function modifierSessionDuPlanning($id,
                                       $id_forum,
						               $id_session,
						               $debut,
						               $fin,
						               $id_salle,$keynote) {
        $requete  = 'UPDATE afup_forum_planning SET ';
        $requete .= ' id_forum = '.$this->_bdd->echapper($id_forum).', ';
        $requete .= ' id_session = '.$this->_bdd->echapper($id_session).', ';
        $requete .= ' debut = '.$this->_bdd->echapper($debut).', ';
        $requete .= ' fin = '.$this->_bdd->echapper($fin).', ';
        $requete .= ' keynote = '.$this->_bdd->echapper($keynote).', ';
        $requete .= ' id_salle = '.$this->_bdd->echapper($id_salle).' ';
        $requete .= ' WHERE id = '.(int)$id;
        return $this->_bdd->executer($requete);
    }

    function ajouterSessionDansPlanning($id_forum,
						                $id_session,
						                $debut,
						                $fin,
						                $id_salle) {
        $donnees = array(
            $this->_bdd->echapper($id_forum),
            $this->_bdd->echapper($id_session),
            $this->_bdd->echapper($debut),
            $this->_bdd->echapper($fin),
            $this->_bdd->echapper($id_salle),
        );

        $requete  = ' INSERT INTO afup_forum_planning';
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
        $requete  = ' SELECT ';
        $requete .= '  se.session_id, ';
        $requete .= '  se.* ';
        $requete .= ' FROM ';
        $requete .= '  afup_sessions se ';
        $requete .= ' LEFT JOIN ';
        $requete .= '  afup_forum_planning pl ';
        $requete .= ' ON se.session_id = pl.id_session';
        $requete .= ' WHERE se.id_forum = '.$this->_bdd->echapper($id_forum);
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
        
        require_once dirname(__FILE__).'/AFUP_Forum.php';
        $forum = new AFUP_Forum($this->_bdd);
        $forum_details = $forum->obtenir($id_forum);
        
        $repertoire = new DirectoryIterator(dirname(__FILE__)."/../../htdocs/templates/".$forum_details['path']."/resumes/");
        foreach($repertoire as $file) {
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

    function obtenirListeSessionsPourConferencier($id_forum,$id_conferencier)
    {
        $requete  = ' SELECT ';
        $requete .= '  se.session_id, ';
        $requete .= '  se.* ';
        $requete .= ' FROM ';
        $requete .= '  afup_sessions se ';
        $requete .= ' INNER JOIN afup_conferenciers_sessions cs ';
        $requete .= '  ON cs.session_id = se.session_id ';
        $requete .= ' WHERE se.id_forum = '.$this->_bdd->echapper($id_forum);
        $requete .= ' AND cs.conferencier_id = '.$this->_bdd->echapper($id_conferencier);
        $requete .= ' AND se.genre != 9 ';
        $requete .= ' ORDER BY ';
        $requete .= '  se.titre';

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirListeSessionsPlannifies($id_forum)
    {
        $requete  = ' SELECT ';
        $requete .= " ( SELECT CONCAT(c.prenom, ' ', c.nom,' - ', c.societe )  FROM afup_conferenciers_sessions cs INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id WHERE cs.session_id = se.session_id order by c.conferencier_id asc limit 1) as conf1 ,
                      ( SELECT CONCAT(c.prenom, ' ', c.nom,' - ', c.societe)  FROM afup_conferenciers_sessions cs INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id WHERE cs.session_id = se.session_id order by c.conferencier_id asc limit 1,1) as conf2 , ";

        $requete .= '  se.*, ';
        $requete .= '  IF(se.journee = 1, "boss", IF(se.journee = 2, "geek", "boss geek")) as journee, ';
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
        $requete .= ' WHERE se.id_forum = '.$this->_bdd->echapper($id_forum);
        $requete .= ' AND se.genre != 9 ';
        $requete .= ' AND se.plannifie = 1';
        $requete .= ' ORDER BY ';
        $requete .= '  pl.debut, sa.nom ,se.titre';

        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirListeSessionsNotees($id_forum   = null)
    {
        $requete  = ' SELECT ';
        $requete .= " ( SELECT CONCAT(c.prenom, ' ', c.nom,' - ', c.societe)  FROM afup_conferenciers_sessions cs INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id WHERE cs.session_id = s.session_id order by c.conferencier_id asc limit 1) as conf1 ,
                      ( SELECT CONCAT(c.prenom, ' ', c.nom,' - ', c.societe)  FROM afup_conferenciers_sessions cs INNER JOIN afup_conferenciers c ON c.conferencier_id = cs.conferencier_id WHERE cs.session_id = s.session_id order by c.conferencier_id asc limit 1,1) as conf2 , ";
        $requete .= '  SUM(no.note) as note, ';
        $requete .= '  s.* ';
        $requete .= ' FROM ';
        $requete .= '  afup_sessions s ';
        $requete .= ' INNER JOIN afup_sessions_note no ';
        $requete .= '  ON s.session_id = no.session_id ';
        $requete .= ' WHERE s.id_forum = '.$this->_bdd->echapper($id_forum);
        $requete .= ' GROUP BY no.session_id ';
        $requete .= ' ORDER BY note DESC';


        return $this->_bdd->obtenirTous($requete);
    }

    function obtenirListeProjets($id_forum   = null,
                          $champs     = 's.*',
                          $ordre      = 's.date_soumission',
                          $associatif = false,
                          $filtre     = false,
                          $only_ids = array())
    {
        $requete  = ' SELECT ';
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
        $requete .= ' WHERE c.id_forum = '.$this->_bdd->echapper($id_forum);
        if ($filtre) {
            $requete .= ' AND s.titre LIKE \'%' . $filtre . '%\' ';
        }
        if ($only_ids) {
            $requete .= ' AND s.session_id IN (' . implode($only_ids,', ') . ') ';
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

    function obtenirListeProjetsPlannifies($id_forum   = null,
                          $champs     = 's.*',
                          $ordre      = 's.date_soumission',
                          $associatif = false)
    {
        $requete  = ' SELECT ';
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
        $requete .= ' WHERE c.id_forum = '.$this->_bdd->echapper($id_forum);
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

    function obtenirListeSessions($id_forum   = null,
                          $champs     = 's.*',
                          $ordre      = 's.date_soumission',
                          $associatif = false,
                          $filtre     = false,
                          $type = 'session')
    {
        $requete  = ' SELECT ';
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
        $requete .= ' WHERE c.id_forum = '.$this->_bdd->echapper($id_forum);
        if ($filtre) {
            $requete .= ' AND s.titre LIKE \'%' . $filtre . '%\' ';
        }
        switch ($type)
        {
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
                                  $biographie)
    {
        $requete  = 'UPDATE afup_conferenciers SET ';
        $requete .= ' id_forum = '.$this->_bdd->echapper($id_forum).', ';
        $requete .= ' civilite = '.$this->_bdd->echapper($civilite).', ';
        $requete .= ' nom = '.$this->_bdd->echapper($nom).', ';
        $requete .= ' prenom = '.$this->_bdd->echapper($prenom).', ';
        $requete .= ' email = '.$this->_bdd->echapper($email).', ';
        $requete .= ' societe = '.$this->_bdd->echapper($societe).', ';
        $requete .= ' biographie = '.$this->_bdd->echapper($biographie).' ';
        $requete .= ' WHERE conferencier_id = '.(int)$id;

        return $this->_bdd->executer($requete);
    }

    function ajouterConferencier($id_forum, $civilite, $nom, $prenom, $email, $societe, $biographie)
    {
        $donnees = array(
            $this->_bdd->echapper($id_forum),
            $this->_bdd->echapper($civilite),
            $this->_bdd->echapper($nom),
            $this->_bdd->echapper($prenom),
            $this->_bdd->echapper($email),
            $this->_bdd->echapper($societe),
            $this->_bdd->echapper($biographie),
        );

        $requete  = ' INSERT INTO afup_conferenciers';
        $requete .= '  (id_forum, civilite, nom, prenom, email, societe, biographie)';
        $requete .= ' VALUES ';
        $requete .= '  (' . implode(',', $donnees) . ')';

        if ($this->_bdd->executer($requete) === false) {
            return false;
        }
        return $this->_bdd->obtenirUn('select LAST_INSERT_ID()');
    }

    public function modifierSession($id, $id_forum, $date_soumission, $titre, $abstract, $journee, $genre, $plannifie)
    {
        $requete  = 'UPDATE afup_sessions SET ';
        $requete .= ' id_forum = '.$this->_bdd->echapper($id_forum).', ';
        $requete .= ' date_soumission = '.$this->_bdd->echapper($date_soumission).', ';
        $requete .= ' titre = '.$this->_bdd->echapper($titre).', ';
        $requete .= ' abstract = '.$this->_bdd->echapper($abstract).', ';
        $requete .= ' journee = '.$this->_bdd->echapper($journee).', ';
        $requete .= ' genre = '.$this->_bdd->echapper($genre).', ';
        $requete .= ' plannifie = '.$this->_bdd->echapper($plannifie).' ';
        $requete .= ' WHERE session_id = '.(int)$id;

        return $this->_bdd->executer($requete);
    }

    public function ajouterSession($id_forum, $date_soumission, $titre, $abstract, $journee, $genre, $plannifie= 0)
    {
        $donnees = array(
            $this->_bdd->echapper($id_forum),
            $this->_bdd->echapper($date_soumission),
            $this->_bdd->echapper($titre),
            $this->_bdd->echapper($abstract),
            $this->_bdd->echapper($journee),
            $this->_bdd->echapper($genre),
            $this->_bdd->echapper($plannifie),
        );

        $requete  = ' INSERT INTO afup_sessions';
        $requete .= '  (id_forum, date_soumission, titre, abstract, journee, genre, plannifie)';
        $requete .= ' VALUES ';
        $requete .= '  (' . implode(',', $donnees) . ')';

        $res = $this->_bdd->executer($requete);
        if ($res === false) {
            return false;
        }
        return $this->_bdd->obtenirUn('select LAST_INSERT_ID()');
    }

    function delierSession($session_id) {
        $requete  = ' DELETE FROM afup_conferenciers_sessions ';
        $requete .= ' WHERE session_id = '.(int)$session_id;

        return $this->_bdd->executer($requete);
    }

    public function lierConferencierSession($conferencier_id, $session_id)
    {
        $donnees = array(
            $this->_bdd->echapper($conferencier_id),
            $this->_bdd->echapper($session_id),
        );

        $requete  = ' REPLACE INTO afup_conferenciers_sessions';
        $requete .= '  (conferencier_id, session_id) ';
        $requete .= ' VALUES ';
        $requete .= ' (' . implode(',', $donnees) . ')';

        return $this->_bdd->executer($requete);
    }

    /**
     * Envoi un email de confirmation au conférencier et mets en copie le bureau
     */
    public function envoyerEmail($session_id)
    {
        require_once dirname(__FILE__).'/AFUP_Configuration.php';
        $configuration = new AFUP_Configuration(dirname(__FILE__).'/../../configs/application/config.php');

        $requete = '
        select prenom, nom, email
        from afup_conferenciers
            inner join afup_conferenciers_sessions
            on afup_conferenciers.conferencier_id=afup_conferenciers_sessions.conferencier_id
        where
            afup_conferenciers_sessions.session_id=' . $this->_bdd->echapper($session_id);

        $conferenciers = $this->_bdd->obtenirTous($requete);

        $corps  = "Bonjour, \n\n";
        $corps .= "Nous avons bien enregistré votre soumission pour le forum PHP.\n";
        $corps .= "Vous recevrez une réponse prochainement.\n\n";
        $corps .= "Le bureau\n\n";
        $corps .= $configuration->obtenir('afup|raison_sociale')."\n";
        $corps .= $configuration->obtenir('afup|adresse')."\n";
        $corps .= $configuration->obtenir('afup|code_postal')." ".$configuration->obtenir('afup|ville')."\n";

        foreach ($conferenciers as $personne) {
            $ok = AFUP_Mailing::envoyerMail(
                            $configuration->obtenir('mails|email_expediteur'),
                            array($personne['email'], $personne['nom']. ' ' . $personne['prenom']),
                            "Soumission de proposition au Forum PHP\n",
                            $corps);
            // $mail->AddBCC('bureau@fup.org', 'Bureau');
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

        $requete = 'insert into afup_sessions_note (session_id, note, salt, date_soumission)
            values (' . implode(',', $donnees) . ')';

        return $this->_bdd->executer($requete);
    }

    function obtenirGrainDeSel($user_id)
    {
        list($usec, $sec) = explode(" ", microtime());
        return md5($user_id . ((float)$usec + (float)$sec));
    }

    function envoyerResumeVote($salt, $user_id)
    {
        $requete  = 'SELECT';
        $requete .= '  nom, prenom, email ';
        $requete .= 'FROM';
        $requete .= '  afup_personnes_physiques ';
        $requete .= 'WHERE';
        $requete .= '  id=' . $this->_bdd->echapper($user_id);

        $resultat = $this->_bdd->obtenirEnregistrement($requete);

        require_once 'Afup/AFUP_Configuration.php';
        $configuration = $GLOBALS['AFUP_CONF'];

        $requete = 'select titre, note
            from afup_sessions_note inner join afup_sessions on
            afup_sessions_note.session_id=afup_sessions.session_id
            where salt=' . $this->_bdd->echapper($salt);

        $resultat = $this->_bdd->obtenirEnregistrement($requete);

        $sujet = "Vos votes de session\n";

        $corps  = "Bonjour, \n\n";
        $corps .= "Nous avons bien enregistré votre vote sur les sessions du forum.\n\n";
        $corps .= $resultat['titre'] . ' ' . $resultat['note'] . "\n";
        $corps .= "le grain de sel pour retrouver l'enregistrement dans la base est $salt";
        $corps .= "\nLe bureau\n\n";
        $corps .= $configuration->obtenir('afup|raison_sociale')."\n";
        $corps .= $configuration->obtenir('afup|adresse')."\n";
        $corps .= $configuration->obtenir('afup|code_postal')." ".$configuration->obtenir('afup|ville')."\n";

        $ok = AFUP_Mailing::envoyerMail(
                $GLOBALS['conf']->obtenir('mails|email_expediteur'),
                array($resultat['email'], $resultat['nom']. ' ' . $resultat['prenom']),
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

        $requete = 'insert into afup_sessions_vote (id_personne_physique,
        id_session, a_vote) values (' . implode(',', $donnees) . ')';

        return $this->_bdd->executer($requete);
    }

    function dejaVote($id_user, $id_session)
    {
        $requete = 'select count(*) from afup_sessions_vote
        where id_personne_physique=' . $this->_bdd->echapper($id_user)
        . ' and id_session=' . $this->_bdd->echapper($id_session);

        return (bool)$this->_bdd->obtenirUn($requete);
    }
}
