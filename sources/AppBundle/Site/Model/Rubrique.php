<?php
namespace AppBundle\Site\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class Rubrique implements NotifyPropertyInterface
{
    use NotifyProperty;

    private $id;
    private $id_personne_physique;
    private $id_parent;
    private $nom;
    private $raccourci;
    private $descriptif;
    private $contenu;
    private $position;
    private $icone;
    private $date;
    private $etat;
    private $pagination;
    private $feuille_associee;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getIdPersonnePhysique() {
        return $this->id_personne_physique;
    }

    public function setIdPersonnePhysique($id) {
        $this->id_personne_physique = $id;
    }

    public function getIdParent() {
        return $this->id_parent;
    }
    
    public function setIdParent($id) {
        $this->id_parent = $id;
    }

    public function getNom() {
        return $this->nom;
    }
    
    public function setNom($nom) {
        $this->nom = $nom;
    }

    public function getRaccourci() {
        return $this->raccourci;
    }
    
    public function setRaccourci($raccourci) {
        $this->raccourci = $raccourci;
    }

    public function getDescriptif() {
        return $this->descriptif;
    }
    
    public function setDescriptif($descriptif) {
        $this->descriptif = $descriptif;
    }

    public function getContenu() {
        return $this->contenu;
    }
    
    public function setContenu($contenu) {
        $this->contenu = $contenu;
    }

    public function getPosition() {
        return $this->position;
    }
    
    public function setPosition($position) {
        $this->position = $position;
    }

    public function getIcone() {
        return $this->icone;
    }
    
    public function setIcone($icone) {
        $this->icone = $icone;
    }

    public function getDate() {
        return $this->date;
    }
    
    public function setDate($date) {
        $this->date = $date;
    }

    public function getEtat() {
        return $this->etat;
    }
    
    public function setEtat($etat) {
        $this->etat = $etat;
    }

    public function getPagination() {
        return $this->pagination;
    }
    
    public function setPagination($pagination) {
        $this->pagination = $pagination;
    }

    public function getFeuilleAssociee() {
        return $this->feuille_associee;
    }
    
    public function setFeuilleAssociee($feuille_associee) {
        $this->feuille_associee = $feuille_associee;
    }
}