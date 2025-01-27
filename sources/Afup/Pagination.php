<?php

declare(strict_types=1);

namespace Afup\Site;

class Pagination
{
    private $page_courante;
    private $nombre_elements_par_page;
    private $nombre_elements_total;
    private $genere_route;

    public function __construct($page_courante, $nombre_elements_par_page, $nombre_elements_total, $genere_route)
    {
        $this->page_courante = $page_courante;
        $this->nombre_elements_par_page = $nombre_elements_par_page;
        $this->nombre_elements_total = $nombre_elements_total;
        $this->genere_route = $genere_route;
    }

    public function __toString()
    {
        global $smarty;

        $page_max = ceil($this->nombre_elements_total / $this->nombre_elements_par_page);
        $pages = [];
        for ($i = 1; $i <= $page_max; $i++) {
            $pages[] = $i;
        }

        $smarty->assign('pagination_page_courante', $this->page_courante);
        $smarty->assign('pagination_pages', $pages);

        $smarty->register_function('pagination_genere_route', $this->genere_route, false);

        ob_start();
        $smarty->display('pagination.html');
        return ob_get_clean();
    }
}
