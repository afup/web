<?php

use Afup\Site\Forum\Inscriptions;
use Afup\Site\Forum\Forum;
use Afup\Site\Forum\Facturation;
use Afup\Site\Utils\Pays;

require_once __DIR__ . '/../../../include/prepend.inc.php';
ini_set('display_errors', 'On');
error_reporting(E_ALL);
if (isset($_GET['event']) === false || ctype_digit($_GET['event']) === false) {
    die('Error - no id specified');
}
$forumId = (int)$_GET['event'];

/**
 * @var $eventRepository \AppBundle\Model\Repository\EventRepository
 */
$eventRepository = $services->get('RepositoryFactory')->get(\AppBundle\Model\Repository\EventRepository::class);
$event = $eventRepository->get($forumId);

if ($event === null) {
    die('Event does not exists');
}
// Get a random list of 10 talks
/**
 * @var $talkRepository \AppBundle\Model\Repository\TalkRepository
 */
$talkRepository = $services->get('RepositoryFactory')->get(\AppBundle\Model\Repository\TalkRepository::class);
$talks = $talkRepository->getTalksToRateByEvent($event);

echo $twig->render(
    'event/vote/liste.html.twig',
    [
        'talks' => $talks,
        'tokenManager' => $services->get('security.csrf.token_manager')
    ]
);