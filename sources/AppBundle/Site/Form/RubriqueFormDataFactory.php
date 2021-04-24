<?php

namespace AppBundle\Site\Form;

use AppBundle\Site\Model\Rubrique;

class RubriqueFormDataFactory
{
    /** @return RubriqueEditFormData */
    public function fromRubrique (Rubrique $rubrique)
    {
        $data = new RubriqueEditFormData();
        $data->nom = $rubrique->getNom();
        $data->descriptif = $rubrique->getDescriptif();
        $data->contenu = $rubrique->getContenu();
        $data->icone = './templates/site/images/'.$rubrique->getIcone();
        $data->raccourci = $rubrique->getRaccourci();
        $data->parent = $rubrique->getIdParent();
        $data->auteur = $rubrique->getIdPersonnePhysique();
        $data->date = new \DateTime(date('d-m-Y',$rubrique->getDate()));
        $data->position = $rubrique->getPosition();
        $data->etat = $rubrique->getEtat();
        $data->pagination = $rubrique->getPagination();
        $data->feuille_associee = $rubrique->getFeuilleAssociee();
        return $data;
    }

    public function toRubrique(RubriqueEditFormData $data, Rubrique $rubrique)
    {
        $rubrique->setNom($data->nom);
        $rubrique->setDescriptif($data->descriptif);
        $rubrique->setContenu($data->contenu);
        $rubrique->setRaccourci($data->raccourci);
        $rubrique->setIdParent(intval($data->parent));
        $rubrique->setIdPersonnePhysique(intval($data->auteur));
        if(!is_null($data->date)) {
            $rubrique->setDate(strtotime($data->date->format('d-m-Y')));
        }
        $rubrique->setPosition($data->position);
        $rubrique->setEtat($data->etat);
        $rubrique->setPagination(intval($data->pagination));
        $rubrique->setFeuilleAssociee(intval($data->feuille_associee));
    }
}