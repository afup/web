<?php

declare(strict_types=1);

use Afup\Site\Utils\SymfonyKernel;
use Symfony\Component\HttpFoundation\Request;

require_once __DIR__ . '/../../../sources/Afup/Bootstrap/Http.php';
/*
 * Define LOADED_USING_INDEX to true
 * See compta_journal.php first lines as example for security check.
 */
define('PAGE_LOADED_USING_INDEX', true);

/**
 * Dans le cadre de la migration progressive on veut pouvoir faire traiter des pages legacy par symfony.
 * Pour cela on fait traiter également la requete par symfony. Pour que cela fonctionne, on ne peut pas appeller
 * la méthode habituelle Request::createFromGlobals puis la faire traiter par le kernel, sinon on déclenche une 404
 * (en effet la home du site, par exemple, est une route inconnue pour symfony).
 * Pour éviter cela, on crée une requete vers la route "/association/void-route" qui nous permet simplement d'effectuer
 * le rendu twig.
 * Pourquoi ne pas directement appeler le rendu twig ? Et bien car certaines variables globales dans twig ne sont
 * définies par symfony qye dans le contexte d'une requete. Par exemple app.session est défini lors d'une requete.
 * Le template exploitant les sessions (pour afficher des flashbags) on ne peut se permettre de simplement appeler twig.
 * On est alors obligés de passer par le kernel et par la requete appelant une route bidon.
 */
$server = $_SERVER;
if ('cli-server' === PHP_SAPI) {
    if (array_key_exists('HTTP_CONTENT_LENGTH', $_SERVER)) {
        $server['CONTENT_LENGTH'] = $_SERVER['HTTP_CONTENT_LENGTH'];
    }
    if (array_key_exists('HTTP_CONTENT_TYPE', $_SERVER)) {
        $server['CONTENT_TYPE'] = $_SERVER['HTTP_CONTENT_TYPE'];
    }
}
$server['REQUEST_URI'] = '/admin/void';
$server['LEGACY_REFERER'] = $_SERVER['REQUEST_URI'];

$request = new Request($_GET, $_POST, [], $_COOKIE, $_FILES, $server);

$kernel = new SymfonyKernel($request);

$response = $kernel->getResponse();
$response->send();

$kernel->getKernel()->terminate($request, $response);
