<?php

declare(strict_types=1);
namespace Afup\Site\Corporate;

use Afup\Site\Utils\Configuration;
use Symfony\Component\Security\Core\User\UserInterface;

class Page
{
    public $route = "";
    public $content;
    public $title;


    /**
     * @var Configuration
     */
    public $conf;

    /**
     * @var _Site_Base_De_Donnees
     */
    private $bdd;

    public function __construct($bdd = false)
    {
        $this->bdd = $bdd ?: new _Site_Base_De_Donnees();
        $this->conf = $GLOBALS['AFUP_CONF'];
    }

    public function definirRoute($route): void
    {
        $this->route = $route;
        switch (true) {
            case preg_match("%\s*/[0-9]*/\s*%", (string) $this->route):
                [, $id, ] = explode("/", (string) $this->route);
                $article = new Article($id, $this->bdd);
                $article->charger();
                $this->title = $article->titre;
                $this->content = $article->afficher();
                break;

            case preg_match("%s*/[0-9]*%", (string) $this->route):
                [, $id] = explode("/", (string) $this->route);
                $rubrique = new Rubrique($id, $this->bdd);
                $rubrique->charger();
                $this->title = $rubrique->nom;
                $this->content = $rubrique->afficher();
                break;

            default:
                $accueil = new Accueil($this->bdd);
                $accueil->charger();
                $this->title = "promouvoir le PHP aupr&egrave;s des professionnels";
                $this->content = $accueil->afficher();
        }
    }

    public function community(): string
    {
        $branche = new Branche($this->bdd);
        return $branche->naviguer(5, 2);
    }

    public function header($url = null, UserInterface $user = null): string
    {
        $branche = new Branche($this->bdd);
        $url = urldecode((string) $url);
        $str = '<ul>';

        $feuillesEnfants = $branche->feuillesEnfants(Feuille::ID_FEUILLE_HEADER);

        if ($user instanceof UserInterface) {
            $feuillesEnfants[] = [
                'id' => PHP_INT_MAX,
                'id_parent' => Feuille::ID_FEUILLE_HEADER,
                'nom' => 'Espace membre',
                'lien' => '/member',
                'alt' => '',
                'position' => '999',
                'date' => null,
                'etat' => '1',
                'image' => null,
                'patterns' => "#/admin/company#",
            ];
        } else {
            $feuillesEnfants[] = [
                'id' => PHP_INT_MAX - 1,
                'id_parent' => Feuille::ID_FEUILLE_HEADER,
                'nom' => 'Se connecter',
                'lien' => '/member',
                'alt' => '',
                'position' => '999',
                'date' => null,
                'etat' => '1',
                'image' => null,
                'patterns' => null,
                'class' => 'desktop-hidden',
            ];
        }

        foreach ($feuillesEnfants as $feuille) {
            $isCurrent = false;
            if ($feuille['patterns']) {
                foreach (explode(PHP_EOL, (string) $feuille['patterns']) as $pattern) {
                    $pattern = trim($pattern);
                    if ($pattern === '') {
                        continue;
                    }

                    if (preg_match($pattern, $url)) {
                        $isCurrent = true;
                    }
                }
            }

            if (str_contains($url, (string) $feuille['lien'])) {
                $isCurrent = true;
            }

            if (false === $isCurrent) {
                $enfants = $branche->feuillesEnfants($feuille['id']);
                foreach ($enfants as $feuilleEnfant) {
                    foreach ($branche->feuillesEnfants($feuilleEnfant['id']) as $feuillesEnfant2) {
                        if (str_contains($url, (string) $feuillesEnfant2['lien'])) {
                            $isCurrent = true;
                        }
                    }
                }
            }

            $class = $isCurrent ? " subheader-current " : "";

            if (isset($feuille['class'])) {
                $class .= ' ' . $feuille['class'];
            }

            $str .= sprintf("<li class='%s'><a href='%s'>%s</a></li>", $class, $feuille['lien'], $feuille['nom']);
        }

        return $str . '<ul>';
    }

    public function content()
    {
        return $this->content;
    }

    public function social(): string
    {
        return
            '<ul id="menufooter-share">
                <li>
                    <a href="' . Site::WEB_PATH . Site::WEB_PREFIX . Site::WEB_QUERY_PREFIX . 'faq/53/comment-contacter-l-afup" class="spriteshare spriteshare-mail">Nous contacter</a>
                </li>
                <li>
                    <a href="http://www.facebook.com/fandelafup" class="spriteshare spriteshare-facebook">L\'AFUP sur Facebook</a>
                </li>
                <li>
                    <a href="https://twitter.com/afup" class="spriteshare spriteshare-twitter">L\'AFUP sur Twitter</a>
                </li>
            </ul>
                ';
    }

    /**
     * @return array{nom: mixed, items: mixed}[]
     */
    public function footer(): array
    {
        $branche = new Branche($this->bdd);

        $footerColumns = [];
        foreach ($branche->feuillesEnfants(Feuille::ID_FEUILLE_FOOTER) as $feuilleColonne) {
            $footerColumns[] = [
                'nom' => $branche->getNom($feuilleColonne['id']),
                'items' => $branche->feuillesEnfants($feuilleColonne['id']),
            ];
        }

        return $footerColumns;
    }

    public function getRightColumn(): Branche
    {
        $branche = new Branche($this->bdd);
        $branche->navigation_avec_image(true);
        return $branche;
    }
}
