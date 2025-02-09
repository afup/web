<?php

declare(strict_types=1);
namespace Afup\Site\Utils;

use Afup\Site\Droits;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Diverses méthodes permettant de se simplifier la vie
 */
class Utils
{
    const TICKETING_VAT_RATE = 0.1;
    const MEMBERSHIP_FEE_VAT_RATE = 0.2;

    public static function fabriqueDroits(TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker): Droits
    {
        return new Droits($tokenStorage, $authorizationChecker);
    }

    /**
     * Get either a Gravatar URL or complete image tag for a specified email address.
     *
     * @param string $email The email address
     * @param int $s Size in pixels, defaults to 80px [ 1 - 512 ]
     * @param string $d Default imageset to use [ 404 | mm | identicon | monsterid | wavatar ]
     * @param string $r Maximum rating (inclusive) [ g | pg | r | x ]
     * @param boolean $img True to return a complete IMG tag False for just the URL
     * @param array $atts Optional, additional key/value attributes to include in the IMG tag
     * @return String containing either just a URL or a complete image tag
     * @source http://gravatar.com/site/implement/images/php/
     */
    public static function get_gravatar($email, $s = 80, $d = 'mm', $r = 'g', $img = false, $atts = []): string
    {
        $url = 'https://www.gravatar.com/avatar/';
        $url .= md5(strtolower(trim($email)));
        $url .= "?s=$s&d=$d&r=$r";
        if ($img) {
            $url = '<img src="' . $url . '"';
            foreach ($atts as $key => $val) {
                $url .= ' ' . $key . '="' . $val . '"';
            }
            $url .= ' />';
        }
        return $url;
    }

    public static function cryptFromText($text): string
    {
        // return base64_encode(mcrypt_cbc(MCRYPT_TripleDES, 'PaiementFactureAFUP_AFUP', $text, MCRYPT_ENCRYPT, '@PaiFact'));
        $text = (string) $text;
        if (strlen($text) % 8 !== 0) {
            $text = str_pad($text, strlen($text) + 8 - strlen($text) % 8, "\0");
        }

        $key = 'PaiementFactureAFUP_AFUP';
        $iv = '@PaiFact';

        return base64_encode(openssl_encrypt($text, 'des-ede3-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv));
    }

    public static function decryptFromText($text): string
    {
        // return trim(mcrypt_cbc(MCRYPT_TripleDES, 'PaiementFactureAFUP_AFUP', base64_decode(str_replace(' ', '+', $text)), MCRYPT_DECRYPT, '@PaiFact'));

        $ref = base64_decode(str_replace(' ', '+', $text));

        $key = 'PaiementFactureAFUP_AFUP';
        $iv = '@PaiFact';

        return trim(openssl_decrypt($ref, 'des-ede3-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv));
    }
}
