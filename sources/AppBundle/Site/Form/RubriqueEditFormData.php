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
     *     minHeight = 43,
     *     maxHeight = 37
     * )     */
    public $icone;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    public $raccourci;

    /**
     * @Assert\Type(
     *     type="object",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    public $parent;

    /**
     * @Assert\Type(
     *     type="object",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
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
    public $etat;

    /**
     * @Assert\Type(
     *     type="object",
     *     message="The value {{ value }} is not a valid {{ type }}."
     * )
     */
    public $feuille_associee;

}