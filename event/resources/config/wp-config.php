<?php
/**
 * La configuration de base de votre installation WordPress.
 *
 * Ce fichier contient les réglages de configuration suivants : réglages MySQL,
 * préfixe de table, clefs secrètes, langue utilisée, et ABSPATH.
 * Vous pouvez en savoir plus à leur sujet en allant sur 
 * {@link http://codex.wordpress.org/fr:Modifier_wp-config.php Modifier
 * wp-config.php}. C'est votre hébergeur qui doit vous donner vos
 * codes MySQL.
 *
 * Ce fichier est utilisé par le script de création de wp-config.php pendant
 * le processus d'installation. Vous n'avez pas à utiliser le site web, vous
 * pouvez simplement renommer ce fichier en "wp-config.php" et remplir les
 * valeurs.
 *
 * @package WordPress
 */

// ** Réglages MySQL - Votre hébergeur doit vous fournir ces informations. ** //
/** Nom de la base de données de WordPress. */
define('DB_NAME', 'event');

/** Utilisateur de la base de données MySQL. */
define('DB_USER', 'afup');

/** Mot de passe de la base de données MySQL. */
define('DB_PASSWORD', 'afup');

/** Adresse de l'hébergement MySQL. */
define('DB_HOST', 'dbevent');

/** Jeu de caractères à utiliser par la base de données lors de la création des tables. */
define('DB_CHARSET', 'utf8mb4');

/** Type de collation de la base de données. 
  * N'y touchez que si vous savez ce que vous faites. 
  */
define('DB_COLLATE', '');

/**#@+
 * Clefs uniques d'authentification et salage.
 *
 * Remplacez les valeurs par défaut par des phrases uniques !
 * Vous pouvez générer des phrases aléatoires en utilisant 
 * {@link https://api.wordpress.org/secret-key/1.1/salt/ le service de clefs secrètes de WordPress.org}.
 * Vous pouvez modifier ces phrases à n'importe quel moment, afin d'invalider tous les cookies existants.
 * Cela forcera également tous les utilisateurs à se reconnecter.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'xXp5lR3Pw.;Wc2,`&8.)$|8^*Z-Z6I<8z*!1+8TERo$UA9hg 7~I/%cxdXCNKA>6');
define('SECURE_AUTH_KEY',  'a|TULivMo#3*j[n+J9C?CMoRqLWg+y&XVI-kKvJK^-J MR{Wbr;FlBWyz!b{HxG[');
define('LOGGED_IN_KEY',    'UGiF=|)N|#x8S+pX&y-`=?Ok/KRJt}(aQ }OSH846;IM,5~Pd(Yy:i>JWVev~#/o');
define('NONCE_KEY',        'wI1?~(uW[kns(ifyrX$(^rnTX,EsBq0%crDCm?94lio6Bdj%nBw(i[U5-/$(.+sX');
define('AUTH_SALT',        '[8N2^E?cK#{*u82h/*+hkf-)-@{E+z+5o%S/?p*?2]<P(^Kn=;+N-|4d-JjCOK14');
define('SECURE_AUTH_SALT', ')e*e^zYy,3-gA0#{knh{H7+1V$D)rmg()9!O2:Yiopm4qFQ6$}Ih/s8,Zjcac1L6');
define('LOGGED_IN_SALT',   '&hcm.U;DIY|C4r`PGz=wcU86GW||EMD8v_-6qs1P88E:jKe=01yA9%S&y.Co+`W7');
define('NONCE_SALT',       '&u_%N*/=lpw<0~]ktU|1$Sz#77,F!c_Cjx W]/OP<z.S+rFG?O5%dT+C)r/=_&f;');
/**#@-*/

/**
 * Préfixe de base de données pour les tables de WordPress.
 *
 * Vous pouvez installer plusieurs WordPress sur une seule base de données
 * si vous leur donnez chacune un préfixe unique. 
 * N'utilisez que des chiffres, des lettres non-accentuées, et des caractères soulignés!
 */
$table_prefix  = 'wp_';

/** 
 * Pour les développeurs : le mode deboguage de WordPress.
 * 
 * En passant la valeur suivante à "true", vous activez l'affichage des
 * notifications d'erreurs pendant votre essais.
 * Il est fortemment recommandé que les développeurs d'extensions et
 * de thèmes se servent de WP_DEBUG dans leur environnement de 
 * développement.
 */ 
define('WP_DEBUG', true);


define('WP_SITEURL', 'http://' . $_SERVER['HTTP_HOST']);
define('WP_HOME', 'http://' . $_SERVER['HTTP_HOST']);


/* C'est tout, ne touchez pas à ce qui suit ! Bon blogging ! */

/** Chemin absolu vers le dossier de WordPress. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Réglage des variables de WordPress et de ses fichiers inclus. */
require_once(ABSPATH . 'wp-settings.php');

define('FS_METHOD','direct');

