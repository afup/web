<?php

declare(strict_types=1);

use Afup\Site\Corporate\Feuille;
use Phinx\Seed\AbstractSeed;

class Feuilles extends AbstractSeed
{
    public function run(): void
    {
        $data = [
            [
                'id' => Feuille::ID_FEUILLE_HEADER,
                'id_parent' => 0,
                'nom' => 'Zone "header"',
                'lien' => '/',
                'alt' => '',
                'position' => 9,
                'date' => 978303600,
                'etat' => -1,
                'image' => null,
                'patterns' => null,
            ],
            [
                'id' => 24,
                'id_parent' => Feuille::ID_FEUILLE_HEADER,
                'nom' => 'Actualités',
                'lien' => '/news/',
                'alt' => '',
                'position' => 0,
                'date' => 1254002400,
                'etat' => 1,
                'image' => null,
                'patterns' => "#/news/\d*-.*#",
            ],
            [
                'id' => 55,
                'id_parent' => Feuille::ID_FEUILLE_HEADER,
                'nom' => 'Antennes',
                'lien' => '/meetups/',
                'alt' => '',
                'position' => 1,
                'date' => 1254002400,
                'etat' => 1,
                'image' => null,
                'patterns' => null,
            ],
            [
                'id' => 44,
                'id_parent' => Feuille::ID_FEUILLE_HEADER,
                'nom' => 'Vidéos',
                'lien' => '/talks/',
                'alt' => '',
                'position' => 5,
                'date' => 1418770800,
                'etat' => 1,
                'image' => null,
                'patterns' => null,
            ],
            [
                'id_parent' => Feuille::ID_FEUILLE_HEADER,
                'nom' => 'Veille',
                'position' => 6,
                'lien' => '/association/techletter',
                'etat' => 1,
            ],
            [
                'id' => 88,
                'id_parent' => Feuille::ID_FEUILLE_HEADER,
                'nom' => 'Boutique',
                'lien' => 'https://shop.afup.org',
                'alt' => '',
                'position' => 7,
                'date' => 1539727200,
                'etat' => 1,
                'image' => null,
                'patterns' => null,
            ],
            [
                'id' => 89,
                'id_parent' => Feuille::ID_FEUILLE_HEADER,
                'nom' => 'Membres',
                'lien' => '/profile/company',
                'alt' => '',
                'position' => 8,
                'date' => 1700077154,
                'etat' => 1,
                'image' => null,
                'patterns' => null,
            ],
            [
                'id' => Feuille::ID_FEUILLE_ANTENNES,
                'id_parent' =>null,
                'nom' => 'Second Menu Antennes',
                'lien' => '/',
                'etat' => 1,
            ],
            [
                'id_parent' =>Feuille::ID_FEUILLE_ANTENNES,
                'nom' => 'Meetups',
                'lien' => '/meetups/',
                'etat' => 1,
            ],
            [
                'id_parent' =>Feuille::ID_FEUILLE_ANTENNES,
                'nom' => 'Liste des antennes',
                'lien' => '/association/antennes',
                'etat' => 1,
            ],
            [
                'id_parent' => Feuille::ID_FEUILLE_HEADER,
                'nom' => 'Plan du site',
                'lien' => '/plan-du-site',
                'date' => 1732710484,
                'etat' => 1,
            ],
        ];

        $data = array_merge($data, $this->prepareFeuilles($this->getFooter(), Feuille::ID_FEUILLE_FOOTER));

        $table = $this->table('afup_site_feuille');
        $table->truncate();

        $table
            ->insert($data)
            ->save()
        ;
    }

    private function getFooter(): array
    {
        return [
            [
                'nom' => 'Navigation',
                'children' => [
                    [
                        'nom' => 'Accueil',
                        'lien' => '/',
                    ],
                    [
                        'nom' => 'Actualités',
                        'lien' => '/news',
                    ],
                    [
                        'nom' => 'Plan du site',
                        'lien' => '/plan-du-site',
                    ],
                ],
            ],
            [
                'nom' => 'Association / Antennes',
                'children' => [
                    [
                        'nom' => 'Adhésion',
                        'lien' => '/association/devenir-membre',
                    ],
                    [
                        'nom' => 'Liste des antennes',
                        'lien' => '/association/antennes',
                    ],
                    [
                        'nom' => 'Meetups',
                        'lien' => '/meetups/',
                    ],
                ],
            ],
            [
                'nom' => 'Réseau AFUP',
                'children' => [
                    [
                        'nom' => 'AFUP Day 2019',
                        'lien' => 'https://event.afup.org',
                    ],
                    [
                        'nom' => 'Baromètre des salaires',
                        'lien' => 'https://barometre.afup.org',
                    ],
                    [
                        'nom' => 'Planète PHP',
                        'lien' => 'https://www.planete-php.fr',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return mixed[]
     */
    private function prepareFeuilles(array $items, $parentId, &$baseId = null): array
    {
        if (null === $baseId) {
            $baseId = 1000;
        }

        $preparedFeuiles = [];
        foreach ($items as $item) {
            $lien = null;
            if (isset($item['lien'])) {
                $lien = $item['lien'];
            }
            $preparedFeuiles[$currentId] = [
                'id' => ($currentId = ++$baseId),
                'nom' => $item['nom'],
                'lien' => $item['lien'] ?? null,
                'id_parent' => $parentId,
                'etat' => 1,
            ];

            if (isset($item['children'])) {
                $preparedFeuiles = array_merge($preparedFeuiles, $this->prepareFeuilles($item['children'], $currentId, $baseId));
            }
        }

        return $preparedFeuiles;
    }
}
