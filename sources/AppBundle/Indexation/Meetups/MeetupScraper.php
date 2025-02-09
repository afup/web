<?php

declare(strict_types=1);

namespace AppBundle\Indexation\Meetups;

use AppBundle\Event\Model\Meetup;
use AppBundle\Offices\OfficesCollection;
use DOMDocument;
use DOMXPath;
use Exception;
use InvalidArgumentException;

class MeetupScraper
{
    const MEETUP_URL = 'https://www.meetup.com/fr-FR/';

    /**
     *
     * @return array<Meetup>
     *
     * @throws Exception
     * @throws InvalidArgumentException
     */
    public function getEvents()
    {
        try {
            $antennes = $this->getAntennesFromOfficesCollection();

            $eventsArray = [];
            foreach ($antennes as $antenneKey => $antenne) {
                if (array_key_exists('meetup_urlname', $antenne)) {
                    //Si l'antenne a une meetup_urlname
                    $meetupUrl = $this->getMeetupUrlNameFromAntenneArray($antenne);
                    $meetupAntenneName = $antenneKey;

                    $xpath = $this->getDomContent($meetupUrl);

                    $events = $xpath->query("//*[contains(@id, 'event-card')]");
                    foreach ($events as $event) {
                        try {
                            if (!$event instanceof \DOMElement) {
                                throw new \Exception('Élement DOM de type invalide');
                            }

                            $eventUrl = $event->getAttribute('href');

                            if (preg_match('/\/events\/(\d+)\//', $eventUrl, $matches)) {
                                $id = (int) $matches[1];
                            } else {
                                throw new Exception(sprintf('Pas d\'id pour cet évent de l\'antenne %s', $antenne));
                            }

                            $dateString = $xpath->query(".//time", $event)->item(0)->nodeValue;
                            $dateTime = (new MeetupDateTimeConverter())->convertStringToDateTime($dateString);

                            $title = $xpath->query(".//span[contains(@class, 'cardTitle')]", $event)->item(0)->nodeValue;

                            $descriptionElements = $xpath->query("//div[contains(@class, 'utils_cardDescription__alO8K')]");

                            $description = '';
                            foreach ($descriptionElements as $descriptionElement) {
                                $description .= ' ' . $descriptionElement->nodeValue;
                            }

                            $eventsArray[$meetupAntenneName][] = (new Meetup())
                                ->setId($id)
                                ->setDate($dateTime)
                                ->setTitle($title)
                                ->setDescription($description)
                                ->setAntenneName($meetupAntenneName);
                        } catch (Exception $e) {
                            throw new Exception('Problème à la construction d\'un évenement', $e->getCode(), $e);
                        }
                    }
                }
            }
        } catch (Exception $e) {
            throw new Exception('Problème à la construction de la liste des évenements', $e->getCode(), $e);
        }

        if ([] === $eventsArray) {
            throw new Exception('Le DOM sur Meetup a très certainement changé (aucun event détecté)');
        }

        return $eventsArray;
    }

    /**
     * Récupère et charge les données de la page pour une meetup url donnée
     *
     * @throws Exception
     */
    public function getDomContent(string $antenneUrl): \DOMXPath
    {
        $url = self::MEETUP_URL . $antenneUrl;
        $content = file_get_contents($url);

        if (strpos($content, 'Groupe introuvable')) {
            throw new Exception(sprintf('Antenne url icorrecte %s', $url), 500);
        }

        // Charger le contenu dans un objet DOMDocument
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Désactiver les erreurs de libxml pour éviter les messages d'erreur lors de l'analyse HTML
        $dom->loadHTML($content);
        libxml_clear_errors(); // Effacer les erreurs de libxml après l'analyse HTML

        return new DOMXPath($dom);
    }

    /**
     * @throws Exception
     */
    public function getAntennesFromOfficesCollection(): array
    {
        $offices = (new OfficesCollection())->getAll();
        if ([] === $offices) {
            throw new Exception("The antennes array is invalid or is empty");
        }

        return $offices;
    }

    /**
     * @param OfficesCollection $array
     * @return string
     * @throws InvalidArgumentException
     */
    private function getMeetupUrlNameFromAntenneArray($array)
    {
        return $this->getArrayValueByKey('meetup_urlname', $array);
    }

    /**
     * @param string $key
     * @param array|OfficesCollection $array
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getArrayValueByKey($key, $array)
    {
        if (!isset($array[$key])) {
            throw new InvalidArgumentException("The '$key' does not exist in the given array.");
        }

        return $array[$key];
    }
}
