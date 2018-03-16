<?php

namespace AppBundle\TechLetter\Model;

class TechLetterFactory
{
    public static function createTechLetterFromJson($json)
    {
        $array = json_decode($json, true);

        $articles = $projects = [];
        foreach ($array['projects'] as $project) {
            $projects[] = new Project($project['url'], $project['name'], $project['description']);
        }

        foreach ($array['articles'] as $article) {
            $articles[] = new Article($article['url'], $article['title'], $article['host'], $article['readingTime'], $article['excerpt']);
        }

        $firstNews = $secondNews = null;

        if (isset($array['firstNews']) && $array['firstNews'] !== null) {
            $firstNews = new News($array['firstNews']['url'], $array['firstNews']['title'], \DateTimeImmutable::createFromFormat('Y-m-d', $array['firstNews']['date']));
        }

        if (isset($array['secondNews']) && $array['secondNews'] !== null) {
            $secondNews = new News($array['secondNews']['url'], $array['secondNews']['title'], \DateTimeImmutable::createFromFormat('Y-m-d', $array['secondNews']['date']));
        }

        return new TechLetter(
            $firstNews,
            $secondNews,
            $articles,
            $projects
        );
    }
}
