<?php

declare(strict_types=1);

namespace AppBundle\Event\Talk;

/**
 * Paramètres d'URL de la liste des talks (`/talks/`), tels qu'écrits par
 * l'urlSync d'instantsearch.js v1 (`htdocs/js/talk/list.js`).
 *
 * Seules `/talks/` et une facette unique (speaker ou événement) sont des pages
 * à indexer ; toute recherche, pagination ou autre filtre en est une déclinaison.
 */
final readonly class TalkListQuery
{
    public const string FACET_SPEAKER = 'speakers.label';
    public const string FACET_EVENT = 'event.title';

    /**
     * Paramètres de l'état Algolia, hors `q`, `p` et `fR` traités à part :
     * `hPP` (résultats par page), `idx` (index), `is_v` (version d'instantsearch),
     * `dFR` (facettes disjonctives), `nR` (filtres numériques), `fE` (exclusions),
     * `hFR` (facettes hiérarchiques), `tR` (tags).
     */
    private const array SEARCH_STATE_PARAMETERS = ['hPP', 'idx', 'is_v', 'dFR', 'nR', 'fE', 'hFR', 'tR'];

    /**
     * @param array<string, mixed> $query
     */
    public function __construct(private array $query) {}

    /**
     * Valeurs de la facette donnée dans `fR`. Une valeur qui n'est pas une
     * chaîne est renvoyée vide, pour qu'elle ne corresponde à rien.
     *
     * @return list<string>
     */
    public function getFacetValues(string $facet): array
    {
        $values = $this->getFacetRefinements()[$facet] ?? [];
        if (!is_array($values)) {
            $values = [$values];
        }

        return array_values(array_map(
            static fn(mixed $value): string => is_string($value) ? $value : '',
            $values,
        ));
    }

    public function isIndexable(): bool
    {
        $search = $this->query['q'] ?? '';
        if (!is_string($search) || trim($search) !== '') {
            return false;
        }

        $page = $this->query['p'] ?? 0;
        if ((!is_string($page) && !is_int($page)) || (int) $page >= 1) {
            return false;
        }

        foreach (self::SEARCH_STATE_PARAMETERS as $parameter) {
            if (array_key_exists($parameter, $this->query)) {
                return false;
            }
        }

        $refinements = $this->getFacetRefinements();
        $refinementsCount = 0;
        foreach ($refinements as $facet => $values) {
            if ($facet !== self::FACET_SPEAKER && $facet !== self::FACET_EVENT) {
                return false;
            }
            $refinementsCount += is_array($values) ? count($values) : 1;
        }

        return $refinementsCount <= 1;
    }

    /**
     * @return array<array-key, mixed>
     */
    private function getFacetRefinements(): array
    {
        $refinements = $this->query['fR'] ?? [];

        return is_array($refinements) ? $refinements : ['' => $refinements];
    }
}
