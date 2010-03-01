<?php
require_once dirname(__FILE__) .'/../../../sources/Afup/Bootstrap/Http.php';

// TODO: Mettre cela dans la base de données
$sponsors=array(
    array('nom'   => 'Ajorolap',
          'site'  => 'www.ajorolap.fr',
          'logo'  => 'logo_ajorolap.gif',
          'texte' => 'Plus que de l\'analyse statistique, AJOROLAP ajoute de l\'intelligence et de l\'expertise en automatisant la génération de cubes et en simplifiant les restitutions des études. AJOROLAP est un produit complet qui contient à la fois un client OLAP et des outils de reporting pour faire parler vos données : un grapheur, un tableur, un cartographieur et un reporteur.'),
    array('nom'   => 'Mandriva',
          'site'  => 'www.mandriva.com',
          'logo'  => 'logo_mandriva.gif',
          'texte' => 'Mandriva c\'est :<ul><li>la 1ère distribution Linux en Europe</li><li>Un des trois acteurs mondiaux du marché Linux</li><li>Des références importantes comme : France Télécom R&D, les Ministères de la Culture, de l\'Equipement et de l\'Agriculture, Dassault Aviation.</li><li>De forts partenariats : HP&reg;, Intel&reg;, AMD&trade;, IBM&reg;, NVIDIA&reg;...</li><li>La distribution la plus facile d\'utilisation, déjà 4 à 8 millions d\'utilisateurs dans le monde !</li></ul>'),
    array('nom'   => 'MySQL',
          'site'  => 'www-fr.mysql.com',
          'logo'  => 'logo_mysql.gif',
          'texte' => 'MySQL AB est la compagnie qui développe, effectue l\'assistance et commercialise le serveur de base de données MySQL mondialement. Notre mission est de rendre une gestion avancée de données disponible et accessible à tout le monde, et de contribuer à la construction des systèmes et produits à grand volume et à mission critique de demain.<br />MySQL en France propose du <a href="http://www-fr.mysql.com/support-and-consulting.html">consulting</a> ainsi que des <a href="http://formation.anaska.fr/formation-mysql.php">formations</a>. <br/> Contact : Marjorie Toucas, Tel : 0800.908.683'),
    array('nom'   => 'WaterProof Software',
          'site'  => 'www.waterproof.fr',
          'logo'  => 'logo_waterproof.gif',
          'texte' => 'WaterProof Software concoit et développe des logiciels dédié à l\'amélioration de l\'efficacité des sociétés utilisant PHP. Parmi nos solutions, on trouve PHPEdit, un des principaux environnements de développement pour PHP, téléchargé plus de 500.000 fois et wIT, notre solution de gestion de rapports utilisateurs. Tous deux sont testable gratuitement sur notre <a href="http://www.waterproof.fr/">site internet</a> sur lequel vous pourrez trouver plus d\'informations sur nos activités et nos produits.')
    );
$smarty->assign('sponsors', $sponsors);

$partenaires=array(
    array('nom'   => 'Eyrolles',
          'site'  => 'www.editions-eyrolles.com',
          'logo'  => 'logo_eyrolles.gif',
          'texte' => 'Les Editions Eyrolles ont placé PHP au coeur de leur offre Développeurs, de l\'initiation (<a href="http://www.editions-eyrolles.com/Livre/9782212114072/php-5">manuels avec cours et exercices</a>, <a href="http://www.editions-eyrolles.com/Livre/9782212116786/php-mysql-et-javascript">apprentissage par la pratique</a>) à l\'exploitation professionnelle (<a href="http://www.editions-eyrolles.com/Livre/9782212116694/php-5-avance">livres de référence</a>, <a href="http://www.editions-eyrolles.com/Livre/9782212112344/php-5">études de cas détaillées</a>). Au-delà de la maîtrise de PHP, chaque ouvrage offre un véritable savoir-faire métier au développeur. Suivez les nouveautés Eyrolles en vous abonnant au fil RSS <a href="http://www.editions-eyrolles.com/rss.php?q=php">http://www.editions-eyrolles.com/rss.php?q=php</a> !'),
    array('nom'   => 'Programmez !',
          'site'  => 'www.programmez.com',
          'logo'  => 'logo_programmez.gif',
          'texte' => 'Avec plus de 30.000 lecteurs mensuels, PROGRAMMEZ ! s\'est imposé comme un magazine de référence des développeurs.'));
$smarty->assign('partenaires', $partenaires);


$smarty->display('sponsors.html');
?>