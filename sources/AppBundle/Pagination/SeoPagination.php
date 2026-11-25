<?php

declare(strict_types=1);

namespace AppBundle\Pagination;

/**
 * Choisit les pages liées depuis une page d'une liste paginée, pour que toute page
 * soit à quelques clics de la première : la première et la dernière page, les voisines
 * de la page courante, et des jalons placés en pourcentage de la distance qui les sépare.
 *
 * Toute page est à 3 clics au plus de la première jusqu'à 203 pages.
 */
final class SeoPagination
{
    /** Nombre de pages liées de chaque côté de la page courante. */
    private const int SIBLINGS = 4;

    /** Jusqu'à ce nombre de pages, toutes sont liées. */
    private const int SHOW_ALL_UNDER = 10;

    /** Positions des jalons, en pourcentage de la distance à couvrir. */
    private const array MILESTONES = [5, 10, 25, 50, 75, 90, 95];

    public static function getLastPage(int $numberOfItems, int $itemsPerPage): int
    {
        return max(1, (int) ceil($numberOfItems / $itemsPerPage));
    }

    /**
     * Pages à afficher, triées, en trois groupes : les jalons avant la page courante,
     * la suite continue de pages qui l'entoure, les jalons après. Un séparateur n'a
     * sa place qu'entre deux groupes.
     *
     * @return array{before: list<int>, around: list<int>, after: list<int>}
     */
    public static function getPages(int $currentPage, int $lastPage): array
    {
        if ($lastPage <= self::SHOW_ALL_UNDER) {
            return ['before' => [], 'around' => range(1, $lastPage), 'after' => []];
        }

        $pages = [
            1,
            $lastPage,
            ...range(max(1, $currentPage - self::SIBLINGS), min($lastPage, $currentPage + self::SIBLINGS)),
        ];
        foreach (self::MILESTONES as $milestone) {
            $pages[] = (int) floor($milestone * ($currentPage - self::SIBLINGS) / 100);
            $pages[] = $currentPage + (int) ceil($milestone * ($lastPage - $currentPage - self::SIBLINGS) / 100);
        }

        $pages = array_values(array_filter(
            array_unique($pages),
            static fn(int $page): bool => $page >= 1 && $page <= $lastPage,
        ));
        sort($pages);

        // La suite continue de pages qui contient la page courante.
        $start = $end = (int) array_search($currentPage, $pages, true);
        while ($start > 0 && $pages[$start - 1] === $pages[$start] - 1) {
            $start--;
        }
        while ($end < count($pages) - 1 && $pages[$end + 1] === $pages[$end] + 1) {
            $end++;
        }

        return [
            'before' => array_slice($pages, 0, $start),
            'around' => array_slice($pages, $start, $end - $start + 1),
            'after' => array_slice($pages, $end + 1),
        ];
    }
}
