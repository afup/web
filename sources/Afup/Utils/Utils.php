<?php
namespace Afup\Site\Utils;

use Afup\Site\AuthentificationInterfaceWiki;
use Afup\Site\AuthentificationWiki;
use Afup\Site\Droits;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Diverses méthodes permettant de se simplifier la vie
 */
class Utils
{
    /**
     * Recupere un objet de type Afup\Site\Droits
     *
     * @param  object $bdd Instance de la couche d'abstraction à la base de données
     * @access public
     * @return object    Afup\Site\Droits
     */
    public static function fabriqueDroits($bdd, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        // Gestion de l'authentification spécifique au Wiki
        $authentificationWiki = new AuthentificationWiki();

        $droits = new Droits($bdd, $tokenStorage, $authorizationChecker);
        $droits->enregistreAuthentification($authentificationWiki);

        return $droits;
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param string $s Size in pixels, defaults to 80px [ 1 - 512 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boolean $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    public static function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = array())
    {
        $url = 'http://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            $url = '<img src="' . $url . '"';
            foreach ($atts as $key => $val)
                $url .= ' ' . $key . '="' . $val . '"';
            $url .= ' />';
        }
        return $url;
    }
}

?>