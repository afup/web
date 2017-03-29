<?php
use Afup\Site\Corporate\Page;

require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

$page = new Page($bdd);

if (isset($_GET['page']) && $_GET['page'] == 'inscription') {
    header('Location: /pages/administration/index.php?page=inscription');
}

$page->definirRoute(isset($_GET['route']) ? $_GET['route'] : '');
$server = $_SERVER;
if ('cli-server' === PHP_SAPI) {
    if (array_key_exists('HTTP_CONTENT_LENGTH', $_SERVER)) {
        $server['CONTENT_LENGTH'] = $_SERVER['HTTP_CONTENT_LENGTH'];
    }
    if (array_key_exists('HTTP_CONTENT_TYPE', $_SERVER)) {
        $server['CONTENT_TYPE'] = $_SERVER['HTTP_CONTENT_TYPE'];
    }
}

/**
 * Ici on forge une requete symfony pointant vers une route qui permet juste d'afficher le contenu qu'on veut afficher
 * Cela permet d'avoir tout le templating géré via symfony mais de conserver la logique d'affichage sur le code legacy.
 * On est obligés par contre de créer la requete manuellement pour truquer l'url.
 */
$request = \Symfony\Component\HttpFoundation\Request::create('/association/void-route', \Symfony\Component\HttpFoundation\Request::METHOD_GET, $_GET, $_COOKIE, $_FILES, $server);
$request->attributes->add(['legacyContent' => [
    'content' => $page->content()
]]);

$kernel = new \Afup\Site\Utils\SymfonyKernel($request);
$response = $kernel->getResponse();
echo $response->getContent();
$kernel->getKernel()->terminate($request, $response);