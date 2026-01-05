<?php

declare(strict_types=1);

namespace Afup\Site\Corporate;

use Afup\Site\Utils\Base_De_Donnees;

class Site
{
    public const WEB_PATH = '/';
    public const WEB_PREFIX = 'pages/site/';
    public const WEB_QUERY_PREFIX = '?route=';

    /**
     * @var Base_De_Donnees
     */
    protected $bdd;

    public function __construct($bdd = false)
    {
        $this->bdd = $bdd ?: new _Site_Base_De_Donnees();
    }

    public static function raccourcir($texte, string $separator = '-'): ?string
    {
        $texte = str_replace('�', 'e', $texte);
        $texte = iconv('ISO-8859-15', 'ASCII//TRANSLIT', trim($texte));
        $pattern = ['/[^a-z0-9]/',
            '/' . $separator . $separator . '+/',
            '/^' . $separator . '/',
            '/' . $separator . '$/'];
        $replacement = [$separator, $separator, '', ''];
        return preg_replace($pattern, $replacement, strtolower($texte));
    }
}
