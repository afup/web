<?php

declare(strict_types=1);

namespace AppBundle\TechLetter\Model;

class TechLetterFactory
{
    public static function createTechLetterFromJson($json): TechLetter
    {
        $array = json_decode((string) $json, true);

        $articles = $projects = [];
        if (isset($array['projects'])) {
            foreach ($array['projects'] as $project) {
                $projects[] = new Project($project['url'], $project['name'], $project['description']);
            }
        }

        if (isset($array['articles'])) {
            foreach ($array['articles'] as $article) {
                $language = $article['language'] ?? 'en';
                $articles[] = new Article($article['url'], $article['title'], $article['host'], $article['readingTime'], $article['excerpt'], $language);
            }
        }

        $firstNews = $secondNews = null;

        if (isset($array['firstNews']) && $array['firstNews'] !== null) {
            $firstNews = new News($array['firstNews']['url'], $array['firstNews']['title'], \DateTimeImmutable::createFromFormat('Y-m-d', (string) $array['firstNews']['date']));
        }

        if (isset($array['secondNews']) && $array['secondNews'] !== null) {
            $secondNews = new News($array['secondNews']['url'], $array['secondNews']['title'], \DateTimeImmutable::createFromFormat('Y-m-d', (string) $array['secondNews']['date']));
        }

        return new TechLetter(
            $firstNews,
            $secondNews,
            $articles,
            $projects
        );
    }
}
