<?php

namespace AppBundle\Indexation\Meetup;

use AppBundle\Indexation\Meetup\Entity\Meetup;
use DateTime;
use DOMXPath;
use DOMDocument;
use DOMNode;
use DOMNodeList;
use IntlDateFormatter;

class MeetupScraper
{
    /**
     *
     * @param string $antenne
     * @return Meetup
     */
    public function getEvents($antenne)
    {
        /** @var DOMXPath */
        $xpath = $this->getDomContent($antenne);

        $events = $xpath->query("//*[contains(@class, 'eventCard--link')]");
        $eventArray = [];
        foreach ($events as $key => $event) {
            $dateElement = $xpath->query("//div[contains(@class, 'eventTimeDisplay')]/time", $event)->item($key);
            $titleElement = $xpath->query("//h2//a[@class='eventCardHead--title']", $event)->item($key);
            $locationElement = $xpath->query("//div[@class='venueDisplay venueDisplay-venue padding--left-half text--secondary text--small']//p", $event)->item($key);
            $descriptionElements = $xpath->query("//div[@class='chunk']//p[contains(@class, 'description-markdown--p')]", $event);

            $formattedDateTime = $this->getStringDateByElement($dateElement);
            $title = $titleElement ? $titleElement->nodeValue : '';
            $formattedDescription = $this->getDescriptionByElements($descriptionElements, $eventArray);
            $location = $locationElement ? $locationElement->nodeValue : '';
            
            $eventArray[] = new Meetup($formattedDateTime, $title, $location, $formattedDescription, null);
        }

        return $eventArray;
    }

    /**
     * Récupère et charge les données de la page
     * 
     * @param string $antenne
     * @return DOMXPath
     */
    private function getDomContent($antenne){
        $url = 'https://www.meetup.com/fr-FR/afup-'.$antenne.'-php';
        $content = file_get_contents($url);
        
        // Charger le contenu dans un objet DOMDocument
        $dom = new DOMDocument();
        libxml_use_internal_errors(true); // Désactiver les erreurs de libxml pour éviter les messages d'erreur lors de l'analyse HTML
        $dom->loadHTML($content);
        libxml_clear_errors(); // Effacer les erreurs de libxml après l'analyse HTML

        return new DOMXPath($dom);
    }

    /**
     * Renvoi la date au format affiché sur Meetup
     *
     * @param DOMNode $dateElement
     * @return string
     */
    private function getStringDateByElement($dateElement){
        if (!$dateElement) {
            return '';
        }

        $dateTimeString = $dateElement->getAttribute('datetime');
        $dateTime = (new DateTime())->setTimestamp(intval($dateTimeString) / 1000);
        $formatter = new IntlDateFormatter("fr_FR", IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
        $formatter->setPattern("EEE dd MMM y, HH:mm 'CET'");
        
        return $formatter->format($dateTime);
    }

    /**
     * Renvoi la description au format affiché sur Meetup
     *
     * @param DOMNodeList $descriptionElements
     * @param array<Meetup> $eventArray

     * @return string
     */
    private function getDescriptionByElements($descriptionElements, $eventArray){
        $description = '';
        foreach ($descriptionElements as $descriptionElement) {
            $description .= $descriptionElement->nodeValue . ' ';
        }
        $description = trim($description);

        // Vérifier que la description n'est pas déjà présente dans un événement précédent
        $isDescriptionDuplicate = false;
        foreach ($eventArray as $event) {
            if ($event->getDescription() == $description) {
                $isDescriptionDuplicate = true;
                return;
            }
        }

        return !$isDescriptionDuplicate ? $description : '';
    }
    
}
