<?php

class AFUP_Planete_Flux
{
    private $_bdd;

    function __construct(&$bdd)
    {
        $this->_bdd = $bdd;   
    }  
    
    function obtenirListeActifs()
    {
        return $this->obtenirListe('*', 'nom', false, false, ' AND etat = 1 ');
    }
    
    function obtenirListe($champs     = '*',
                          $ordre      = 'nom', 
                          $associatif = false, 
                          $filtre     = false,
                          $where      = '')
    {
        $requete  = ' SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= ' FROM';
        $requete .= '  afup_planete_flux ';
        $requete .= ' WHERE 1 ';
        $requete .= $where;
        if ($filtre) {
            $requete .= ' AND nom LIKE \'%' . $filtre . '%\' ';
        }
        $requete .= ' ORDER BY ' . $ordre;
        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {        
            return $this->_bdd->obtenirTous($requete);
        }    
    }

    function obtenir($id, $champs = '*')
    {
        $requete  = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '  afup_planete_flux ';
        $requete .= 'WHERE id=' . $id;
        return $this->_bdd->obtenirEnregistrement($requete);    
    }
    
    function ajouter($nom, $url, $feed, $etat, $id_personne_physique = 0)
    {
        $requete  = 'INSERT INTO ';
        $requete .= '  afup_planete_flux (nom, url, feed, etat, id_personne_physique) ';
        $requete .= 'VALUES (';
        $requete .= $this->_bdd->echapper($nom)             . ',';
        $requete .= $this->_bdd->echapper($url)             . ',';
        $requete .= $this->_bdd->echapper($feed)            . ',';
        $requete .= $etat                                   . ',';
        $requete .= (int)$id_personne_physique              . ')';
        return $this->_bdd->executer($requete);    
        
    }

    function modifier($id, $nom, $url, $feed, $etat, $id_personne_physique = 0)
    {
        $requete  = 'UPDATE ';    
        $requete .= '  afup_planete_flux ';
        $requete .= 'SET';
        $requete .= '  nom='                  . $this->_bdd->echapper($nom)              . ','; 
        $requete .= '  url='                  . $this->_bdd->echapper($url)              . ','; 
        $requete .= '  feed='                 . $this->_bdd->echapper($feed)             . ','; 
        $requete .= '  etat='                 . $this->_bdd->echapper($etat)             . ','; 
        $requete .= '  id_personne_physique=' . (int)$id_personne_physique               . ' '; 
        $requete .= 'WHERE';   
        $requete .= '  id=' . $id;   
        return $this->_bdd->executer($requete);    
    }    

    function supprimer($id)
    {
		$requete = 'DELETE FROM afup_planete_flux WHERE id=' . $id;
		return $this->_bdd->executer($requete);    
    }
    
    function obtenirTousParDateDuDernierBillet()
    {
    	$requete  = 'SELECT ';
    	$requete .= '  MAX(afup_planete_billet.id) as id, ';
    	$requete .= '  afup_planete_flux.nom, ';
    	$requete .= '  afup_planete_flux.url ';
    	$requete .= 'FROM ';
    	$requete .= '  afup_planete_billet ';
		$requete .= 'INNER JOIN ';
    	$requete .= '  afup_planete_flux ';
    	$requete .= 'ON ';
    	$requete .= '  afup_planete_billet.afup_planete_flux_id = afup_planete_flux.id ';
    	$requete .= 'WHERE '; 
    	$requete .= '  afup_planete_billet.etat = 1 ';
    	$requete .= 'AND '; 
    	$requete .= '  afup_planete_flux.etat = 1 ';
    	$requete .= 'GROUP BY '; 
    	$requete .= '  afup_planete_flux.id ';
    	$requete .= 'ORDER BY ';
    	$requete .= '  id DESC ';

    	return $this->_bdd->obtenirTous($requete);
    }
}
