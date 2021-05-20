<?php

namespace AppBundle\Site\Model;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use Symfony\Component\Validator\Constraints as Assert;

class Rubrique implements NotifyPropertyInterface
{
    use NotifyProperty;

    const ID_RUBRIQUE_ACTUALITES = 9;
    const ID_RUBRIQUE_ASSOCIATION = 85;
    const ID_RUBRIQUE_ANTENNES = 84;
    const ID_RUBRIQUE_INFORMATIONS_PRATIQUES = 86;
    const ID_RUBRIQUE_NOS_ACTIONS = 88;

    /**
     * @Assert\Type("integer")
     */
    private $id;
    private $idPersonnePhysique;
    private $idParent;
    private $nom;
    private $raccourci;
    private $descriptif;
    private $contenu;
    private $position;
    private $icone;
    private $date;
    private $etat;
    private $pagination = 0;
    private $feuilleAssociee;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->propertyChanged('id', $this->id, $id);
        $this->id = $id;
    }

    public function getIdPersonnePhysique()
    {
        return $this->idPersonnePhysique;
    }

    public function setIdPersonnePhysique($id)
    {
        $this->propertyChanged('idPersonnePhysique', $this->idPersonnePhysique, $id);
        $this->idPersonnePhysique = $id;
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

    public function getNom()
    {
        return $this->nom;
    }

    public function setNom($nom)
    {
        $this->propertyChanged('nom', $this->nom, $nom);
        $this->nom = $nom;
    }

    public function getRaccourci()
    {
        return $this->raccourci;
    }

    public function setRaccourci($raccourci)
    {
        $this->propertyChanged('raccourci', $this->raccourci, $raccourci);
        $this->raccourci = $raccourci;
    }

    public function getDescriptif()
    {
        return $this->descriptif;
    }

    public function setDescriptif($descriptif)
    {
        $this->descriptif = $descriptif;
        $this->propertyChanged('descriptif', $this->descriptif, $descriptif);
    }

    public function getContenu()
    {
        return $this->contenu;
    }

    public function setContenu($contenu)
    {
        $this->propertyChanged('contenu', $this->contenu, $contenu);
        $this->contenu = $contenu;
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

    public function getIcone()
    {
        return $this->icone;
    }

    public function setIcone($icone)
    {
        $this->propertyChanged('icone', $this->icone, $icone);
        $this->icone = $icone;
    }

    public function getDate()
    {
        return  $this->date;
    }

    public function setDate($date)
    {
        $this->propertyChanged('date', $this->date, $date);
        $this->date = $date;
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

    public function getPagination()
    {
        return $this->pagination;
    }

    public function setPagination($pagination = 0)
    {
        $pagination = is_null($pagination) ? 0 : $pagination;
        $this->propertyChanged('pagination', $this->pagination, $pagination);
        $this->pagination = $pagination;
    }

    public function getFeuilleAssociee()
    {
        return $this->feuilleAssociee;
    }

    public function setFeuilleAssociee($feuilleAssociee)
    {
        $this->propertyChanged('feuilleAssociee', $this->feuilleAssociee, $feuilleAssociee);
        $this->feuilleAssociee = $feuilleAssociee;
    }
}
