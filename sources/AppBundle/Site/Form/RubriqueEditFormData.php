<?php

namespace AppBundle\Site\Form;

use AppBundle\Site\Model\Rubrique;
use Symfony\Component\Validator\Constraints as Assert;

class RubriqueEditFormData
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $nom;

    /**
     * @Assert\Length(max=255)
     */
    public $descriptif;

    /**
     * @Assert\NotBlank()
     */
    public $contenu;

    /**
    * @Assert\Image(
     *     minHeight = 37,
     *     maxHeight = 43
     * )     
     * */
    public $icone;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $raccourci;

   
    public $parent;

   
    public $auteur;

    /**
     * @Assert\Date
     */
    public $date;

    /**
     * @Assert\Type("integer")
     */
    public $position;

     /**
     * @Assert\Type("integer")
     */
    public $pagination;

    /**
     * @Assert\Type("integer")
     */
    public $etat;

  
    public $feuille_associee;

}