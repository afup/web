<?php

declare(strict_types=1);

namespace Afup\Site\Forum;

use Afup\Site\Utils\Base_De_Donnees;

class Inscriptions
{
    public function __construct(private readonly Base_De_Donnees $_bdd) {}

    /**
     * @param int $idForum
     * @param int $idForumPrecedent
     */
    public function obtenirSuivi($idForum, $idForumPrecedent = null): array
    {
        $forum = new Forum($this->_bdd);
        if (null === $idForumPrecedent) {
            $idForumPrecedent = $forum->obtenirForumPrecedent($idForum);
        }

        $now = new \DateTime();
        $dateForum = new \DateTimeImmutable('@' . $forum->obtenir($idForum)['date_fin_vente']);

        $daysToEndOfSales = 0;
        if ($dateForum >= $now) {
            $daysToEndOfSales = $dateForum->diff($now)->format('%r%a');
        }

        $requete = '
        SELECT SUM(nombre) as nombre, jour, id_forum
        FROM (
            SELECT 
              COUNT(*) as nombre, 
              DATEDIFF(FROM_UNIXTIME(date, \'%Y-%m-%d\'), FROM_UNIXTIME(af.date_fin_vente, \'%Y-%m-%d\')) as jour,
              id_forum
            FROM
              afup_inscription_forum i
            RIGHT JOIN afup_forum_tarif aft ON (aft.id = i.type_inscription AND aft.default_price > 0)
            LEFT JOIN afup_forum af ON af.id = i.id_forum
            WHERE
              i.id_forum IN (' . (int) $idForum . ', ' . (int) $idForumPrecedent . ') 
            AND 
              etat <> 1 
            GROUP BY jour, i.id_forum 
            HAVING jour < 0     
            UNION ALL
            SELECT
                SUM(max_invitations) as nombre,
                DATEDIFF(created_on, FROM_UNIXTIME(af.date_fin_vente, \'%Y-%m-%d\')) as jour,
                id_forum
            FROM afup_forum_sponsors_tickets st
            LEFT JOIN afup_forum af ON af.id = st.id_forum
            WHERE
              st.id_forum IN (' . (int) $idForum . ', ' . (int) $idForumPrecedent . ')
            GROUP BY jour, st.id_forum
            HAVING jour < 0     
            ORDER BY jour ASC
        ) all_data
        GROUP BY jour, id_forum
        ';
        $nombre_par_date = $this->_bdd->obtenirTous($requete);

        if ([] === $nombre_par_date) {
            $nombre_par_date = [['jour' => 1]];
        }

        $suivis = [];

        for ($i = $nombre_par_date[0]['jour']; $i <= 0; $i++) {
            $infoForum = array_sum(array_map(function (array $info) use ($i, $idForum) {
                if ($info['id_forum'] == $idForum && $info['jour'] <= $i) {
                    return $info['nombre'];
                }
                return 0;
            }, $nombre_par_date));
            $infoN1 = array_sum(array_map(function (array $info) use ($i, $idForumPrecedent) {
                if ($info['id_forum'] == $idForumPrecedent && $info['jour'] <= $i) {
                    return $info['nombre'];
                }
                return 0;
            }, $nombre_par_date));
            $suivis[$i] = [
                'jour' => $i,
                'n' => $daysToEndOfSales >= $i ? $infoForum : null,
                'n_1' => $infoN1,
            ];
        }

        return [
            'suivi' => $suivis,
            'min' => $nombre_par_date[0]['jour'],
            'max' => $i,
            'daysToEndOfSales' => $daysToEndOfSales,
        ];
    }
}
