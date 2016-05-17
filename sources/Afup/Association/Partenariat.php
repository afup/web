<?php

namespace Afup\Site\Association;
class Partenariat
{
    /**
     * @var \Afup\Site\Utils\Base_De_Donnees
     */
    public $_bdd;

    function __construct(&$bdd)
    {
        $this->_bdd = $bdd;
    }

    function _clean($mot)
    {
        $mot = strtr($mot,
            array('Š' => 'S', 'š' => 's', 'Ð' => 'Dj', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
                'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
                'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
                'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
                'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
                'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
                'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f'));
        $mot = strtolower($mot);
        $mot = str_replace(array('-', ' ', "'"),
            array('', '', ''),
            $mot);
        return $mot;

    }

    function _hash($nom, $prenom)
    {
        return $this->_clean($nom) . '#' . $this->_clean($prenom);
    }

    function verifierMembre($nom, $prenom)
    {
        $hashPersonne = $this->_hash($nom, $prenom);
        $personnesActives = $this->_bdd->obtenirTous('
            SELECT nom, prenom
            FROM afup_personnes_physiques
            WHERE etat = 1'
        );
        foreach ($personnesActives as $p) {
            if ($this->_hash($p['nom'], $p['prenom']) == $hashPersonne) {
                return '<div style="color: #008000">Cette personne est membre actif de l\'AFUP</div>';
            }
        }
        return '<div style="color: #800000">Cette personne n\'est pas membre de l\'AFUP</div>';
    }
}
