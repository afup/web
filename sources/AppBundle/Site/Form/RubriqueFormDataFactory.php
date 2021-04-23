<?php

namespace AppBundle\Site\Form;

use AppBundle\Site\Model\Rubrique;

class RubriqueFormDataFactory
{
    /** @return RubriqueFormData */
    public function fromRubrique (RubriqueEditFormData $data, Rubrique $rubrique)
    {
        $data = new RubriqueFormData();
        $data->nom = $rubrique->getNom();
        $data->descriptif = $rubrique->getDescriptif();
        $data->contenu = $rubrique->getContenu();

        $data->icone = $rubrique->getIcone();

        $data->raccourci = $rubrique->getRaccourci();

        $data->parent = $rubrique->getParent();

        $data->auteur = $rubrique->getAuteur();

        $data->date = $rubrique->getDate();
        $data->position = $rubrique->getPosition();
        $data->etat = $rubrique->getEtat();

        $data->feuille_associee = $rubrique->getFeuilleAssociee();
       
        return $data;
    }
}