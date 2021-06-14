<?php

namespace AppBundle\Site\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;

class Feuille implements NotifyPropertyInterface
{
    use NotifyProperty;

    const ID_FEUILLE_ANTENNES = 71;
    const ID_FEUILLE_ASSOCIATION = 74;
    const ID_FEUILLE_COLONNE_DROITE = 1;
    const ID_FEUILLE_HEADER = 21;
    const ID_FEUILLE_FOOTER = 38;
    const ID_FEUILLE_NOS_ACTIONS = 96;

    private $id;
    private $idParent;
    private $nom;
    private $lien;
    private $alt;
    private $position;
    private $date;
    private $etat;
    private $image;
    private $patterns;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
    }

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom)
    {
        $this->propertyChanged('nom', $this->nom, $nom);
        $this->nom = $nom;
    }

    public function getIdParent()
    {
        return $this->idParent;
    }

    public function setIdParent($id)
    {
        $this->propertyChanged('idParent', $this->idParent, $id);
        $this->idParent = $id;
    }

    public function getLien()
    {
        return $this->lien;
    }

    public function setLien($lien)
    {
        $this->propertyChanged('lien', $this->lien, $lien);
        $this->lien = $lien;
    }

    public function getAlt()
    {
        return $this->alt;
    }

    public function setAlt($alt)
    {
        $this->propertyChanged('alt', $this->alt, $alt);
        $this->alt = $alt;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->propertyChanged('position', $this->position, $position);
        $this->position = $position;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate($date)
    {
        $this->propertyChanged('date', $this->date, $date);
        $this->date = $date;
        return $this;
    }

    public function getEtat()
    {
        return $this->etat;
    }

    public function setEtat($etat)
    {
        $this->propertyChanged('etat', $this->etat, $etat);
        $this->etat = $etat;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->propertyChanged('image', $this->image, $image);
        $this->image = $image;
    }

    public function getPatterns()
    {
        return $this->patterns;
    }

    public function setPatterns($patterns)
    {
        $this->propertyChanged('patterns', $this->patterns, $patterns);
        $this->patterns = $patterns;
    }
}
