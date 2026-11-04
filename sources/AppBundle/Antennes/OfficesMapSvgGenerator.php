<?php

declare(strict_types=1);

namespace AppBundle\Antennes;

/**
 * Génère la carte des antennes de la page /association/antennes.
 *
 * Reproduit le rendu de l'ancienne carte jQuery Mapael : fond de carte décalé dans un
 * viewBox élargi, zones et points des antennes, et légendes (traits, logo, nom) dans le
 * repère du viewBox, aux coordonnées définies dans `Map`.
 */
final class OfficesMapSvgGenerator
{
    private const int VIEWBOX_WIDTH = 870;
    private const int VIEWBOX_HEIGHT = 700;
    private const int INTRINSIC_WIDTH = 600;
    private const int INTRINSIC_HEIGHT = 626;
    private const int MAP_OFFSET_X = 135;
    private const int MAP_OFFSET_Y = 30;

    private const string AREA_COLOR = '#50a0dd';
    private const string ANTENNE_COLOR = '#2b3e74';
    private const string BORDER_COLOR = '#ced8d0';

    private const int PLOT_RADIUS = 10;

    private const int LEGEND_BOX_WIDTH = 150;
    private const int LOGO_WIDTH = 45;
    private const int LOGO_HEIGHT = 50;
    private const int LOGO_MARGIN = 4;
    private const int TEXT_MARGIN = 8;
    private const int TEXT_CENTERING = 23;

    /**
     * @param iterable<Antenne> $antennes
     */
    public function generate(iterable $antennes): string
    {
        $antennes = [...$antennes];

        $xml = new \XMLWriter();
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->setIndentString('  ');

        $xml->startElement('svg');
        $xml->writeAttribute('xmlns', 'http://www.w3.org/2000/svg');
        $xml->writeAttribute('viewBox', sprintf('0 0 %d %d', self::VIEWBOX_WIDTH, self::VIEWBOX_HEIGHT));
        // Taille intrinsèque du fond de carte, comme le faisait Mapael : le CSS l'étire à
        // 100 %, mais quand la hauteur du conteneur n'est pas définie (mobile), c'est ce
        // ratio qui s'applique et qui laisse la place aux légendes qui débordent du viewBox.
        $xml->writeAttribute('width', (string) self::INTRINSIC_WIDTH);
        $xml->writeAttribute('height', (string) self::INTRINSIC_HEIGHT);
        $xml->writeElement('title', "Carte de France des antennes de l'AFUP");

        $xml->startElement('g');
        $xml->writeAttribute('transform', sprintf('translate(%d %d)', self::MAP_OFFSET_X, self::MAP_OFFSET_Y));
        $xml->writeAttribute('fill', self::AREA_COLOR);
        $xml->writeAttribute('stroke', self::BORDER_COLOR);
        $xml->writeAttribute('stroke-width', '1');
        $xml->writeAttribute('stroke-linejoin', 'round');

        $this->writeAreas($xml, $antennes);
        $this->writePlots($xml, $antennes);

        $xml->endElement();

        foreach ($antennes as $antenne) {
            $this->writeLegend($xml, $antenne);
        }

        $xml->endElement();

        return $xml->outputMemory();
    }

    /**
     * @param array<Antenne> $antennes
     */
    private function writeAreas(\XMLWriter $xml, array $antennes): void
    {
        $antenneByArea = [];
        foreach ($antennes as $antenne) {
            foreach ($antenne->departments ?? [] as $department) {
                $antenneByArea['department-' . $department] = $antenne;
            }
            foreach ($antenne->pays ?? [] as $pays) {
                $antenneByArea['country-' . $pays] = $antenne;
            }
        }

        foreach (FranceMap::AREAS as $key => $path) {
            $antenne = $antenneByArea[$key] ?? null;

            if (!$antenne instanceof Antenne) {
                $this->writePath($xml, $key, $path);
                continue;
            }

            $link = $this->link($antenne);
            if ($link !== null) {
                $xml->startElement('a');
                $xml->writeAttribute('href', $link);
                $xml->writeAttribute('aria-label', $antenne->label);
            }

            $this->writePath($xml, $key, $path, self::ANTENNE_COLOR);

            if ($link !== null) {
                $xml->endElement();
            }
        }
    }

    private function writePath(\XMLWriter $xml, string $key, string $path, ?string $fill = null): void
    {
        $xml->startElement('path');
        $xml->writeAttribute('data-id', $key);
        if ($fill !== null) {
            $xml->writeAttribute('fill', $fill);
        }
        $xml->writeAttribute('d', $path);
        $xml->endElement();
    }

    /**
     * @param array<Antenne> $antennes
     */
    private function writePlots(\XMLWriter $xml, array $antennes): void
    {
        foreach ($antennes as $antenne) {
            if (!$antenne->map instanceof Map) {
                continue;
            }

            foreach ([$antenne->map->firstCity, $antenne->map->secondCity] as $city) {
                if (!$city instanceof City) {
                    continue;
                }

                $point = FranceMap::project($city->position->latitude, $city->position->longitude);

                $xml->startElement('circle');
                $xml->writeAttribute('cx', $this->number($point['x']));
                $xml->writeAttribute('cy', $this->number($point['y']));
                $xml->writeAttribute('r', (string) self::PLOT_RADIUS);
                $xml->writeAttribute('fill', self::ANTENNE_COLOR);
                $xml->writeAttribute('stroke-width', '0');
                $xml->endElement();
            }
        }
    }

    private function writeLegend(\XMLWriter $xml, Antenne $antenne): void
    {
        $map = $antenne->map;
        if (!$map instanceof Map) {
            return;
        }

        foreach ([$map->firstCity, $map->secondCity] as $city) {
            if (!$city instanceof City) {
                continue;
            }

            $this->writeLine($xml, $city->firstPoint, $city->secondPoint);
            $this->writeLine($xml, $city->secondPoint, $city->thirdPoint);
        }

        // La légende (logo + nom) s'accroche au troisième point de la première ville.
        $anchor = $map->firstCity->thirdPoint;

        [$logoX, $logoY] = match ($map->legendAttachment) {
            LegendAttachment::Left => [$anchor->x + self::LOGO_MARGIN, $anchor->y - self::LOGO_HEIGHT / 2],
            LegendAttachment::Right => [$anchor->x - self::LOGO_MARGIN - self::LOGO_WIDTH, $anchor->y - self::LOGO_HEIGHT / 2],
            LegendAttachment::Top => [$anchor->x - self::LEGEND_BOX_WIDTH / 2, $anchor->y + self::LOGO_MARGIN],
            LegendAttachment::Bottom => [$anchor->x - self::LEGEND_BOX_WIDTH / 2, $anchor->y - self::LOGO_HEIGHT - self::LOGO_MARGIN],
        };

        // Le nom est à droite du logo, sauf quand la légende s'accroche par la droite.
        [$textX, $textAnchor] = $map->legendAttachment === LegendAttachment::Right
            ? [$logoX - self::TEXT_MARGIN, 'end']
            : [$logoX + self::LOGO_WIDTH + self::TEXT_MARGIN, 'start'];

        $xml->startElement('image');
        $xml->writeAttribute('href', $antenne->logoUrl);
        $xml->writeAttribute('x', $this->number($logoX));
        $xml->writeAttribute('y', $this->number($logoY));
        $xml->writeAttribute('width', (string) self::LOGO_WIDTH);
        $xml->writeAttribute('height', (string) self::LOGO_HEIGHT);
        $xml->writeAttribute('preserveAspectRatio', 'none');
        $xml->endElement();

        $link = $this->link($antenne);
        if ($link !== null) {
            $xml->startElement('a');
            $xml->writeAttribute('href', $link);
        }

        $xml->startElement('text');
        $xml->writeAttribute('x', $this->number($textX));
        $xml->writeAttribute('y', $this->number($logoY + self::TEXT_CENTERING));
        $xml->writeAttribute('dominant-baseline', 'central');
        $xml->writeAttribute('text-anchor', $textAnchor);
        $xml->writeAttribute('font-family', 'Arial');
        $xml->writeAttribute('font-size', '14px');
        $xml->writeAttribute('font-weight', 'bold');
        $xml->text($antenne->label);
        $xml->endElement();

        if ($link !== null) {
            $xml->endElement();
        }
    }

    private function writeLine(\XMLWriter $xml, Point $from, Point $to): void
    {
        $xml->startElement('path');
        $xml->writeAttribute('d', sprintf('M%d,%dL%d,%d', $from->x, $from->y, $to->x, $to->y));
        $xml->writeAttribute('fill', 'none');
        $xml->writeAttribute('stroke', '#000');
        $xml->endElement();
    }

    /**
     * Même lien que le premier réseau social affiché sur la fiche de l'antenne.
     */
    private function link(Antenne $antenne): ?string
    {
        $socials = $antenne->socials;

        return match (true) {
            $socials->blog !== null => $socials->blog,
            $socials->twitter !== null => 'https://twitter.com/' . $socials->twitter,
            $socials->linkedin !== null => 'https://www.linkedin.com/company/' . $socials->linkedin,
            $socials->bluesky !== null => 'https://bsky.app/profile/' . $socials->bluesky,
            $antenne->meetup instanceof Meetup => 'https://www.meetup.com/fr-FR/' . $antenne->meetup->urlName,
            $socials->youtube !== null => $socials->youtube,
            default => null,
        };
    }

    private function number(float|int $value): string
    {
        return (string) round($value, 2);
    }
}
