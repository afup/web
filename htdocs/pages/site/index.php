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
$request = \Symfony\Component\HttpFoundation\Request::create('/association/void-route', \Symfony\Component\HttpFoundation\Request::METHOD_GET, $_GET, $_COOKIE, $_FILES, $server);
$request->attributes->add(['legacyContent' => [
    'content' => $page->content()
]]);

$kernel = new \Afup\Site\Utils\SymfonyKernel($request);
$response = $kernel->getResponse();
echo $response->getContent();
$kernel->getKernel()->terminate($request, $response);
