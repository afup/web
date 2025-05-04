<?php

declare(strict_types=1);

namespace AppBundle\TechLetter;

class DataExtractor
{
    /**
     * @see https://fr.wikipedia.org/wiki/Mot_par_minute
     */
    const WORD_READ_BY_MINUTES = 250;

    /**
     * @return mixed[]
     */
    public function extractDataForTechLetter($url): array
    {
        $urlInfo = parse_url((string) $url);

        $urlCrawler = new UrlCrawler();
        $html = $urlCrawler->crawlUrl($url);

        $parser = new HtmlParser($html);

        /**
         * Certaines données sont représentées sous 2 titres car les différents modèles utilisent des noms différents pour
         * des choses similaires
         * @todo fix it
         */
        $data = [
            'title' => substr((string) $parser->getTitle(), 0, 250),
            'name' => substr((string) $parser->getTitle(), 0, 250),
            'excerpt' => $parser->getMeta('description'),
            'description' => $parser->getMeta('description'),
            'host' => $urlInfo['host'],
        ];

        $richSchema = $parser->getRichSchema();


        $listOfTypes = [
            "NewsArticle",
            "Report",
            "ScholarlyArticle",
            "SocialMediaPosting",
            "TechArticle",
            "Article",
            "BlogPosting",
        ];

        foreach ($richSchema as $schema) {
            if (
                ! isset($schema['@type'])
                || !in_array($schema["@type"], $listOfTypes)
            ) {
                continue;
            }

            if (isset($schema['datePublished'])) {
                $date = new \DateTimeImmutable($schema['datePublished']);
                $data['date'] = $date->format('Y-m-d');
            }
            if (isset($schema['articleBody'])) {
                $body = strip_tags($schema['articleBody']);
                $data['readingTime'] = floor(str_word_count($body) / self::WORD_READ_BY_MINUTES);
            }
        }

        return array_map(fn ($value): string => trim($value), $data);
    }
}
