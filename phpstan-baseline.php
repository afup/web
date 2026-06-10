<?php declare(strict_types = 1);

$ignoreErrors = [];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'https\\://\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Bootstrap/Http.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 5,
	'path' => __DIR__ . '/sources/Afup/Bootstrap/Http.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function substr expects int\\|null, int\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Bootstrap/Http.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method batchLoadMetadata\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Bootstrap/commonStart.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method obtenir\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 4,
	'path' => __DIR__ . '/sources/Afup/Bootstrap/commonStart.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method register_modifier\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Bootstrap/commonStart.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setConfig\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Bootstrap/commonStart.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$locale of class Symfony\\\\Component\\\\Translation\\\\Translator constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Bootstrap/commonStart.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Comptabilite/PDF.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset 0 on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Comptabilite/PDF.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset 1 on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Comptabilite/PDF.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset 2 on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Comptabilite/PDF.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Comptabilite/PDF.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Comptabilite\\\\PDF\\:\\:__construct\\(\\) has parameter \\$orientation with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Comptabilite/PDF.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Comptabilite\\\\PDF\\:\\:__construct\\(\\) has parameter \\$size with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Comptabilite/PDF.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Comptabilite\\\\PDF\\:\\:__construct\\(\\) has parameter \\$unit with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Comptabilite/PDF.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Comptabilite\\\\PDF\\:\\:tableau\\(\\) has parameter \\$data with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Comptabilite/PDF.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Comptabilite\\\\PDF\\:\\:tableau\\(\\) has parameter \\$header with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Comptabilite/PDF.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Comptabilite\\\\PDF\\:\\:tableau\\(\\) has parameter \\$position with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Comptabilite/PDF.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$num of function number_format expects float, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Comptabilite/PDF.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Corporate/Page.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Corporate\\\\Page\\:\\:header\\(\\) has parameter \\$url with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Corporate/Page.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$parentId of method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\FeuilleRepository\\:\\:getFeuillesEnfant\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/sources/Afup/Corporate/Page.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type int\\|null\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Corporate/Page.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$host of method Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\:\\:__construct\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Corporate/_Site_Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$database of method Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\:\\:__construct\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Corporate/_Site_Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$user of method Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\:\\:__construct\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Corporate/_Site_Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#4 \\$password of method Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\:\\:__construct\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Corporate/_Site_Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'elements\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Droits.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'niveau\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/Afup/Droits.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getUser\\(\\) on Symfony\\\\Component\\\\Security\\\\Core\\\\Authentication\\\\Token\\\\TokenInterface\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/Afup/Droits.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Droits\\:\\:chargerToutesLesPages\\(\\) has parameter \\$pages with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Droits.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Afup\\\\Site\\\\Droits\\:\\:\\$_pages type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Droits.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\|false supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 2,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \' AND s\\.titre LIKE \\\\\'%%\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between literal\\-string&non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'conferencier_id\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'file\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'id_forum\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'session_id\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset non\\-falsy\\-string on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 4,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:ajouterSession\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:ajouterSession\\(\\) has parameter \\$abstract with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:ajouterSession\\(\\) has parameter \\$date_soumission with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:ajouterSession\\(\\) has parameter \\$id_forum with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:ajouterSession\\(\\) has parameter \\$titre with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:ajouterSession\\(\\) has parameter \\$useMarkdown with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:dejaVote\\(\\) has parameter \\$id_session with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:dejaVote\\(\\) has parameter \\$id_user with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:delierSession\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:delierSession\\(\\) has parameter \\$session_id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:lierConferencierSession\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:lierConferencierSession\\(\\) has parameter \\$conferencier_id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:lierConferencierSession\\(\\) has parameter \\$session_id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$abstract with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$blogPostUrl with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$date_publication with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$date_soumission with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$id_forum with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$interviewUrl with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$joindin with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$languageCode with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$openfeedbackPath with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$slidesUrl with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$titre with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$transcript with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$tweets with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$use_markdown with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$verbatim with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$video_has_en_subtitles with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$video_has_fr_subtitles with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:modifierSession\\(\\) has parameter \\$youtubeId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:nbVoteSession\\(\\) has parameter \\$id_session with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:obtenirCommentairesPourSession\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:obtenirCommentairesPourSession\\(\\) has parameter \\$id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:obtenirConferenciersPourSession\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:obtenirConferenciersPourSession\\(\\) has parameter \\$id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:obtenirListeProjets\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:obtenirListeProjets\\(\\) has parameter \\$associatif with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:obtenirListeProjets\\(\\) has parameter \\$filtre with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:obtenirListeProjets\\(\\) has parameter \\$id_forum with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:obtenirListeProjets\\(\\) has parameter \\$only_ids with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:obtenirListeSessionsAvecResumes\\(\\) has parameter \\$id_forum with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:obtenirListeSessionsPlannifies\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:obtenirListeSessionsPlannifies\\(\\) has parameter \\$id_forum with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:obtenirSession\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:obtenirSession\\(\\) has parameter \\$complement with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:obtenirSession\\(\\) has parameter \\$id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:supprimerSession\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\AppelConferencier\\:\\:supprimerSession\\(\\) has parameter \\$id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:obtenir\\(\\) expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function implode expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/AppelConferencier.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \' / \' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \' \\<span class\\=…\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'l\\.id_forum \\= \' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between mixed and \' \\: \' results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between mixed and \'\\-\' results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 7,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 8,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'conf1\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'conf2\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'debut\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 7,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'fin\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 7,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'id_salle\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 3,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'jour\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'keynote\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'nom\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 4,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'session_id\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'titre\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset int\\<0, max\\> on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 6,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 5,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$accomodationEnabled with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$chemin_template with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$date_annonce_planning with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$date_debut with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$date_fin with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$date_fin_appel_conferencier with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$date_fin_appel_projet with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$date_fin_prevente with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$date_fin_saisie_nuites_hotel with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$date_fin_saisie_repas_speakers with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$date_fin_vente with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$date_fin_vente_token_sponsor with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$date_fin_vote with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$hasPricesDefinedWithVat with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$logoUrl with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$nb_places with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$placeAddress with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$placeName with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$speakersDinerEnabled with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$text with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$titre with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$transportInformationEnabled with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$voteEnabled with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:ajouter\\(\\) has parameter \\$waitingListUrl with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:genAgenda\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:genAgenda\\(\\) has parameter \\$annee with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:genAgenda\\(\\) has parameter \\$for_bo with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:genAgenda\\(\\) has parameter \\$forum_id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:genAgenda\\(\\) has parameter \\$linkFormat with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:genAgenda\\(\\) has parameter \\$only_data with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$accomodationEnabled with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$chemin_template with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$date_annonce_planning with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$date_debut with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$date_fin with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$date_fin_appel_conferencier with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$date_fin_appel_projet with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$date_fin_prevente with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$date_fin_saisie_nuites_hotel with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$date_fin_saisie_repas_speakers with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$date_fin_vente with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$date_fin_vente_token_sponsor with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$date_fin_vote with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$hasPricesDefinedWithVat with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$logoUrl with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$nb_places with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$placeAddress with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$placeName with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$speakersDinerEnabled with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$text with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$titre with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$transportInformationEnabled with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$voteEnabled with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:modifier\\(\\) has parameter \\$waitingListUrl with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:obtenir\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:obtenir\\(\\) should return array but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:obtenirAgenda\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:obtenirAgenda\\(\\) has parameter \\$forum_id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:obtenirDernier\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:obtenirForumPrecedent\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:obtenirForumPrecedent\\(\\) has parameter \\$id_forum with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:obtenirListActive\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:obtenirListe\\(\\) has parameter \\$filtre with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:obtenirListe\\(\\) has parameter \\$id_forum with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:obtenirListe\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:obtenirListe\\(\\) should return array but returns array\\|false\\.$#',
	'identifier' => 'return.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:supprimer\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:supprimer\\(\\) has parameter \\$id_forum with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$annee of method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:obtenirAgenda\\(\\) expects int\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$date of method Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\:\\:echapperSqlDateFromQuickForm\\(\\) expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 20,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of function count expects array\\|Countable, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$for_bo of method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:lienSeance\\(\\) expects bool, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$timestamp of function date expects int\\|null, int\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$linkFormat of method Afup\\\\Site\\\\Forum\\\\Forum\\:\\:lienSeance\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Part \\$nomSalle \\(mixed\\) of encapsed string cannot be cast to string\\.$#',
	'identifier' => 'encapsedStringPart.nonString',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 15,
	'path' => __DIR__ . '/sources/Afup/Forum/Forum.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "/" between mixed and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Pagination.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method assign\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/Afup/Pagination.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method display\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Pagination.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method register_function\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Pagination.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Pagination\\:\\:__construct\\(\\) has parameter \\$genere_route with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Pagination.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Pagination\\:\\:__construct\\(\\) has parameter \\$nombre_elements_par_page with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Pagination.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Pagination\\:\\:__construct\\(\\) has parameter \\$nombre_elements_total with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Pagination.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Pagination\\:\\:__construct\\(\\) has parameter \\$page_courante with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Pagination.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \' \' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'\\:\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between mixed and \'\\-\' results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 3,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset 0 on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\:\\:__construct\\(\\) has parameter \\$port with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\:\\:echapperSqlDateFromQuickForm\\(\\) has parameter \\$date with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\:\\:echapperSqlDateFromQuickForm\\(\\) should return int\\|string but returns int\\|false\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\:\\:executer\\(\\) should return bool but returns mysqli_result\\|true\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\:\\:getDbLink\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\:\\:obtenirAssociatif\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\:\\:obtenirDernierId\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\:\\:obtenirTous\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$hostname of function mysqli_connect expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$mysql of function mysqli_error expects mysqli, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$mysql of function mysqli_insert_id expects mysqli, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$mysql of function mysqli_query expects mysqli, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 5,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$mysql of function mysqli_real_escape_string expects mysqli, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$mysql of function mysqli_select_db expects mysqli, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$nom of method Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\:\\:selectionnerBase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$result of function mysqli_fetch_array expects mysqli_result, mysqli_result\\|true given\\.$#',
	'identifier' => 'argument.type',
	'count' => 5,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$result of function mysqli_fetch_fields expects mysqli_result, mysqli_result\\|true given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$result of function mysqli_free_result expects mysqli_result, mysqli_result\\|true given\\.$#',
	'identifier' => 'argument.type',
	'count' => 4,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$result of function mysqli_num_fields expects mysqli_result, bool\\|mysqli_result given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$username of function mysqli_connect expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$password of function mysqli_connect expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 2,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\:\\:\\$config type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\:\\:\\$link \\(mysqli\\) does not accept mysqli\\|false\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Base_De_Donnees.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between literal\\-string&non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Configuration.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'database_host\'\\|\'database_name\'\\|\'database_password\'\\|\'database_port\'\\|\'database_user\'\\|\'smtp_host\'\\|\'smtp_password\'\\|\'smtp_port\'\\|\'smtp_tls\'\\|\'smtp_username\' on 0\\|0\\.0\\|\'\'\\|\'0\'\\|array\\{\\}\\|array\\{database_host\\?\\: mixed, database_name\\?\\: mixed, database_password\\?\\: mixed, database_port\\?\\: mixed, database_user\\?\\: mixed, smtp_host\\?\\: mixed, smtp_password\\?\\: string, smtp_port\\?\\: mixed, \\.\\.\\.\\}\\|false\\|null\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Configuration.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'database_host\'\\|\'database_name\'\\|\'database_password\'\\|\'database_port\'\\|\'database_user\'\\|\'smtp_host\'\\|\'smtp_password\'\\|\'smtp_port\'\\|\'smtp_tls\'\\|\'smtp_username\' on 0\\|0\\.0\\|\'\'\\|\'0\'\\|array\\{\\}\\|array\\{database_host\\?\\: mixed, database_name\\?\\: mixed, database_user\\?\\: mixed, database_password\\?\\: mixed, database_port\\?\\: mixed, smtp_host\\?\\: mixed, smtp_port\\?\\: mixed, smtp_tls\\?\\: mixed, \\.\\.\\.\\}\\|false\\|null\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Configuration.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'parameters\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Configuration.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset mixed on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Configuration.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Configuration\\:\\:loadSymfonyParameters\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Configuration.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Configuration\\:\\:obtenir\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Configuration.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Configuration\\:\\:obtenir\\(\\) has parameter \\$cle with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Configuration.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$arrays of function array_merge expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Configuration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Afup\\\\Site\\\\Utils\\\\Configuration\\:\\:\\$values has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Configuration.php',
];
$ignoreErrors[] = [
	'message' => '#^Property Afup\\\\Site\\\\Utils\\\\LegacyConnectionFactory\\:\\:\\$bdd \\(Afup\\\\Site\\\\Utils\\\\Base_De_Donnees\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/LegacyConnectionFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEmail\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Mailing.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function reset expects array\\|object, array\\<AppBundle\\\\Email\\\\Mailer\\\\MailUser\\>\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Mailing.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$replace of function str_replace expects array\\<string\\>\\|string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Mailing.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between mixed and \' \' results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 6,
	'path' => __DIR__ . '/sources/Afup/Utils/PDF_AG.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 7,
	'path' => __DIR__ . '/sources/Afup/Utils/PDF_AG.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\PDF_AG\\:\\:writeRow\\(\\) has parameter \\$row with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/PDF_AG.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$name on AppBundle\\\\Site\\\\Entity\\\\Country\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Pays.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\SymfonyKernel\\:\\:getResponse\\(\\) should return Symfony\\\\Component\\\\HttpFoundation\\\\Response but returns Symfony\\\\Component\\\\HttpFoundation\\\\Response\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/SymfonyKernel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$request of method Symfony\\\\Component\\\\HttpKernel\\\\Kernel\\:\\:handle\\(\\) expects Symfony\\\\Component\\\\HttpFoundation\\\\Request, Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/SymfonyKernel.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Utils.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Utils.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Utils\\:\\:cryptFromText\\(\\) has parameter \\$text with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Utils.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Utils\\:\\:decryptFromText\\(\\) has parameter \\$text with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Utils.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Utils\\:\\:get_gravatar\\(\\) has parameter \\$atts with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Utils.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function base64_encode expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Utils.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\<string\\>\\|string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Utils.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\*" between mixed and \\(float\\|int\\) results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Vat.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\+" between 1 and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 2,
	'path' => __DIR__ . '/sources/Afup/Utils/Vat.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "/" between mixed and \\(float\\|int\\) results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Vat.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Vat\\:\\:getRoundedWithVatPriceFromPriceWithoutVat\\(\\) has parameter \\$priceWithoutVat with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Vat.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Vat\\:\\:getRoundedWithVatPriceFromPriceWithoutVat\\(\\) has parameter \\$vatRate with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Vat.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Vat\\:\\:getRoundedWithoutVatPriceFromPriceWithVat\\(\\) has parameter \\$priceWithVat with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Vat.php',
];
$ignoreErrors[] = [
	'message' => '#^Method Afup\\\\Site\\\\Utils\\\\Vat\\:\\:getRoundedWithoutVatPriceFromPriceWithVat\\(\\) has parameter \\$vatRate with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/Utils/Vat.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 2,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'class\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'elements\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'erreur\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'event_selector_current_id\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'message\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'nom\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'path\' on array\\{scheme\\?\\: string, host\\?\\: string, port\\?\\: int\\<0, 65535\\>, user\\?\\: string, pass\\?\\: string, path\\?\\: string, query\\?\\: string, fragment\\?\\: string\\}\\|false\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getAttributes\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setAttributes\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Function genererFormulaire\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Function obtenirTitre\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Function obtenirTitre\\(\\) has parameter \\$page with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Function obtenirTitre\\(\\) has parameter \\$pages with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Function verifierAction\\(\\) should return string but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$action of class AppBundle\\\\Association\\\\Form\\\\HTML_QuickForm constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/Afup/fonctions.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Entity\\\\Repository\\\\AccountRepository\\:\\:getAllSortedByName\\(\\) should return array\\<AppBundle\\\\Accounting\\\\Entity\\\\Account\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Entity/Repository/AccountRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Entity\\\\Repository\\\\CategoryRepository\\:\\:getAllSortedByName\\(\\) should return array\\<AppBundle\\\\Accounting\\\\Entity\\\\Category\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Entity/Repository/CategoryRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Entity\\\\Repository\\\\EventRepository\\:\\:getAllSortedByName\\(\\) should return array\\<AppBundle\\\\Accounting\\\\Entity\\\\Event\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Entity/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Entity\\\\Repository\\\\PaymentRepository\\:\\:getAllSortedByName\\(\\) should return array\\<AppBundle\\\\Accounting\\\\Entity\\\\Payment\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Entity/Repository/PaymentRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Entity\\\\Repository\\\\ProduitRepository\\:\\:getAllSortedByReference\\(\\) should return array\\<AppBundle\\\\Accounting\\\\Entity\\\\Produit\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Entity/Repository/ProduitRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Entity\\\\Repository\\\\RuleRepository\\:\\:getAllSortedByName\\(\\) should return array\\<AppBundle\\\\Accounting\\\\Entity\\\\Rule\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Entity/Repository/RuleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Accounting\\\\Form\\\\AccountType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/AccountType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Accounting\\\\Form\\\\CategoryType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/CategoryType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Accounting\\\\Form\\\\EventType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/EventType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Accounting\\\\Form\\\\InvoiceType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/InvoiceType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/InvoicingPeriodType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Accounting\\\\Form\\\\InvoicingPeriodType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/InvoicingPeriodType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Accounting\\\\Form\\\\InvoicingRowType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/InvoicingRowType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Accounting\\\\Form\\\\OperationType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/OperationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Accounting\\\\Form\\\\PaymentType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/PaymentType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Accounting\\\\Form\\\\ProduitType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/ProduitType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Accounting\\\\Form\\\\QuotationType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/QuotationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Accounting\\\\Form\\\\RuleType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/RuleType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_flip expects array\\<int\\|string\\>, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/RuleType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Accounting\\\\Form\\\\SearchType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/SearchType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Accounting\\\\Form\\\\TransactionType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/TransactionType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Form\\\\TransactionType\\:\\:buildAccountChoice\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/TransactionType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Form\\\\TransactionType\\:\\:buildCategoryChoice\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/TransactionType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Form\\\\TransactionType\\:\\:buildEventChoice\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/TransactionType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Form\\\\TransactionType\\:\\:buildOperationTypeChoice\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/TransactionType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Form\\\\TransactionType\\:\\:buildPaymentTypeChoice\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/TransactionType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Accounting\\\\Form\\\\TransactionsImportType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Form/TransactionsImportType.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method AppBundle\\\\Association\\\\Model\\\\CompanyMember\\|AppBundle\\\\Association\\\\Model\\\\User\\:\\:getCompanyName\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Invoices/Generator/CompanyMemberInvoiceGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\+" between mixed and 0\\|30\\|90\\|110\\|130\\|160\\|190 results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 4,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/InvoicingPdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\+\\=" between mixed and 30 results in an error\\.$#',
	'identifier' => 'assignOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/InvoicingPdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\InvoicingPdfGenerator\\:\\:output\\(\\) should return string but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/InvoicingPdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\InvoicingPdfGenerator\\:\\:renderLineItems\\(\\) should return array\\{float, float, array\\<string, float\\>\\} but returns array\\{float, float, array\\<int\\|numeric\\-string, float\\>\\}\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/InvoicingPdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of method Afup\\\\Site\\\\Utils\\\\Pays\\:\\:obtenirNom\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/InvoicingPdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$num of function number_format expects float, float\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/InvoicingPdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of method AppBundle\\\\Accounting\\\\InvoicingPdfGenerator\\:\\:formatValue\\(\\) expects float, float\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/InvoicingPdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Accounting\\\\Model\\\\Invoicing\\:\\:\\$countryId \\(string\\) does not accept string\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Invoicing.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'id\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingDetailRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\InvoicingDetailRepository\\:\\:getRowsIdsPerInvoicingId\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingDetailRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\InvoicingDetailRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingDetailRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\InvoicingDetailRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingDetailRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingDetailRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\InvoicingPeriodRepository\\:\\:getCurrentPeriod\\(\\) should return AppBundle\\\\Accounting\\\\Model\\\\InvoicingPeriod but returns AppBundle\\\\Accounting\\\\Model\\\\InvoicingPeriod\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingPeriodRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\InvoicingPeriodRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingPeriodRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\InvoicingPeriodRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingPeriodRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingPeriodRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'next_index\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\InvoicingRepository\\:\\:findOneWithDetails\\(\\) has parameter \\$params with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\InvoicingRepository\\:\\:getInvoicesByPeriodId\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\InvoicingRepository\\:\\:getQuotationsByPeriodId\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\InvoicingRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\InvoicingRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/InvoicingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/TransactionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\TransactionRepository\\:\\:getEntriesPerInvoicingPeriod\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/TransactionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\TransactionRepository\\:\\:getNextTransaction\\(\\) should return AppBundle\\\\Accounting\\\\Model\\\\Transaction\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/TransactionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\TransactionRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/TransactionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\TransactionRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/TransactionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/Model/Repository/TransactionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/TransactionModification.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$comment of method AppBundle\\\\Accounting\\\\Model\\\\Transaction\\:\\:setComment\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Accounting/TransactionModification.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$email of class AppBundle\\\\Email\\\\Mailer\\\\MailUser constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/CompanyMembership/AbstractCompanyReminder.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$email of method AppBundle\\\\Association\\\\Model\\\\SubscriptionReminderLog\\:\\:setEmail\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/CompanyMembership/AbstractCompanyReminder.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$userId of method AppBundle\\\\Association\\\\Model\\\\SubscriptionReminderLog\\:\\:setUserId\\(\\) expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/CompanyMembership/AbstractCompanyReminder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\CompanyMembership\\\\CompanyReminderFactory\\:\\:getReminder\\(\\) has parameter \\$class with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/CompanyMembership/CompanyReminderFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/CompanyMembership/CompanyReminderFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\CompanyMembership\\\\SubscriptionManagement\\:\\:createInvoiceForInscription\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/CompanyMembership/SubscriptionManagement.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Association\\\\Form\\\\AdminCompanyMemberType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/AdminCompanyMemberType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Association\\\\Form\\\\BadgeType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/BadgeType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Association\\\\Form\\\\CompanyEditType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/CompanyEditType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Association\\\\Form\\\\CompanyMemberInvitationType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/CompanyMemberInvitationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Association\\\\Form\\\\CompanyMemberType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/CompanyMemberType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Association\\\\Form\\\\CompanyPublicProfile extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/CompanyPublicProfile.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Association\\\\Form\\\\ContactDetailsType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/ContactDetailsType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Form\\\\HTML_QuickForm\\:\\:getElements\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/HTML_QuickForm.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Association\\\\Form\\\\NearestOfficeChoiceType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/NearestOfficeChoiceType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Association\\\\Form\\\\RegisterUserType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/RegisterUserType.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/TicketEventType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Association\\\\Form\\\\TicketEventType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/TicketEventType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Form\\\\TicketEventType\\:\\:ticketTypesToChoices\\(\\) has parameter \\$ticketTypes with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/TicketEventType.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserBadgeType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getBadge\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserBadgeType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 4,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserBadgeType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Association\\\\Form\\\\UserBadgeType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserBadgeType.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type int\\|null\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserBadgeType.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserBadgeType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$address has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$alternateEmail has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$cellphone has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$city has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$companyId has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$directoryLevel has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$email has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$eventLevel has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$firstname has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$lastname has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$level has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$officeLevel has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$password has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$phone has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$roles has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$status has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$username has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$websiteLevel has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Form\\\\UserEditFormData\\:\\:\\$zipcode has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditFormData.php',
];
$ignoreErrors[] = [
	'message' => '#^Anonymous function should return array but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditType.php',
];
$ignoreErrors[] = [
	'message' => '#^Anonymous function should return non\\-empty\\-string but returns non\\-empty\\-string\\|false\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Association\\\\Form\\\\UserEditType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserEditType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Association\\\\Form\\\\UserType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Form/UserType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\CompanyMember\\:\\:getFormattedRelatedAfupOffices\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/CompanyMember.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\CompanyMember\\:\\:setFormattedRelatedAfupOffices\\(\\) has parameter \\$relatedAfupOffices with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/CompanyMember.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function implode expects array\\<string\\>, list given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/CompanyMember.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\GeneralMeetingVote\\:\\:getAllValues\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/GeneralMeetingVote.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\GeneralMeetingVote\\:\\:getValueLabel\\(\\) should return string\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/GeneralMeetingVote.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\GeneralMeetingVote\\:\\:getVoteLabelsByValue\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/GeneralMeetingVote.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\GeneralMeetingVote\\:\\:isValueAllowed\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/GeneralMeetingVote.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/GeneralMeetingVote.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\CompanyMemberInvitationRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberInvitationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\CompanyMemberInvitationRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberInvitationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\CompanyMemberInvitationRepository\\:\\:loadPendingInvitationsByCompany\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberInvitationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberInvitationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'id\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'id_personne_morale\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'nb\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'raison_sociale\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset 0 on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$nb on mixed\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method bindValue\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method cols\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getBindValues\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getStatement\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 4,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method orderBy\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method where\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 4,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 5,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\CompanyMemberRepository\\:\\:findById\\(\\) has parameter \\$id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\CompanyMemberRepository\\:\\:findById\\(\\) should return AppBundle\\\\Association\\\\Model\\\\CompanyMember\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\CompanyMemberRepository\\:\\:findDisplayableCompanies\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\CompanyMemberRepository\\:\\:getHydratorForCompanyMember\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\CompanyMemberRepository\\:\\:getQueryBuilderWithCompleteCompanyMember\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\CompanyMemberRepository\\:\\:getQueryBuilderWithSubscriptions\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\CompanyMemberRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\CompanyMemberRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\CompanyMemberRepository\\:\\:loadAll\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\CompanyMemberRepository\\:\\:search\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\CompanyMemberRepository\\:\\:searchCompanyMemberSubscriptions\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$hydrator of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Association\\\\Model\\\\CompanyMember\\>\\:\\:getCollection\\(\\) expects CCMBenchmark\\\\Ting\\\\Repository\\\\HydratorInterface\\<mixed\\>\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 4,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$params of method CCMBenchmark\\\\Ting\\\\Query\\\\Query\\:\\:setParams\\(\\) expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$sql of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Association\\\\Model\\\\CompanyMember\\>\\:\\:getPreparedQuery\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$sql of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Association\\\\Model\\\\CompanyMember\\>\\:\\:getQuery\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$callback of function array_filter expects \\(callable\\(mixed\\)\\: bool\\)\\|null, Closure\\(AppBundle\\\\Association\\\\Model\\\\CompanyMember\\)\\: bool given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type U in call to method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Association\\\\Model\\\\CompanyMember\\>\\:\\:getCollection\\(\\)$#',
	'identifier' => 'argument.templateType',
	'count' => 5,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/CompanyMemberRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\GeneralMeetingQuestionRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingQuestionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\GeneralMeetingQuestionRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingQuestionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\GeneralMeetingQuestionRepository\\:\\:loadNextOpenedQuestion\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingQuestionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingQuestionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\GeneralMeetingResponseRepository\\:\\:getByUser\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingResponseRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\GeneralMeetingResponseRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingResponseRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\GeneralMeetingResponseRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingResponseRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingResponseRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'value\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingVoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'weight_sum\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingVoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\GeneralMeetingVoteRepository\\:\\:getResultsForQuestionId\\(\\) should return array\\<string, int\\> but returns array\\<mixed\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingVoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\GeneralMeetingVoteRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingVoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\GeneralMeetingVoteRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingVoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\GeneralMeetingVoteRepository\\:\\:loadByQuestionIdAndUserId\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingVoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\GeneralMeetingVoteRepository\\:\\:loadByQuestionIdAndUserId\\(\\) has parameter \\$questionId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingVoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\GeneralMeetingVoteRepository\\:\\:loadByQuestionIdAndUserId\\(\\) has parameter \\$userId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingVoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingVoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/GeneralMeetingVoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\*" between int and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/SubscriptionReminderLogRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\+" between mixed and 1 results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/SubscriptionReminderLogRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/SubscriptionReminderLogRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\SubscriptionReminderLogRepository\\:\\:getPaginatedLogs\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/SubscriptionReminderLogRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\SubscriptionReminderLogRepository\\:\\:getPaginatedLogs\\(\\) has parameter \\$limit with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/SubscriptionReminderLogRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\SubscriptionReminderLogRepository\\:\\:getPaginatedLogs\\(\\) has parameter \\$page with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/SubscriptionReminderLogRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\SubscriptionReminderLogRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/SubscriptionReminderLogRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\SubscriptionReminderLogRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/SubscriptionReminderLogRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/SubscriptionReminderLogRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\SubscriptionRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/SubscriptionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\SubscriptionRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/SubscriptionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\SubscriptionRepository\\:\\:searchCompanyMemberSubscriptions\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/SubscriptionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\SubscriptionRepository\\:\\:searchMemberSubscriptions\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/SubscriptionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/SubscriptionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset 0 on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$nb on mixed\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method bindValue\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 5,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getBindValues\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getStatement\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 8,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method having\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method join\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method orWhere\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method orderBy\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method where\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 12,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository implements generic interface Symfony\\\\Component\\\\Security\\\\Core\\\\User\\\\UserProviderInterface but does not specify its types\\: TUser$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:getActiveMembers\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:getAdministrators\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:getHydratorForUser\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:getQueryBuilderWithCompleteUser\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:getUsersByEndOfMembership\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:loadActiveUsersByCompany\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:loadAll\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:loadByBadge\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:loadUserByEmailOrAlternateEmail\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:loadUserByEmailOrAlternateEmail\\(\\) has parameter \\$email with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:loadUserByHash\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:loadUserByHash\\(\\) has parameter \\$hash with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:loadUserByUsername\\(\\) should return AppBundle\\\\Association\\\\Model\\\\User but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:search\\(\\) has parameter \\$companyId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:search\\(\\) has parameter \\$direction with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:search\\(\\) has parameter \\$filter with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:search\\(\\) has parameter \\$isCompanyManager with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:search\\(\\) has parameter \\$needsUptoDateMembership with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\Repository\\\\UserRepository\\:\\:search\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$hydrator of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Association\\\\Model\\\\User\\>\\:\\:getCollection\\(\\) expects CCMBenchmark\\\\Ting\\\\Repository\\\\HydratorInterface\\<mixed\\>\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 8,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$params of method CCMBenchmark\\\\Ting\\\\Query\\\\Query\\:\\:setParams\\(\\) expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$sql of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Association\\\\Model\\\\User\\>\\:\\:getPreparedQuery\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 5,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$sql of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Association\\\\Model\\\\User\\>\\:\\:getQuery\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type U in call to method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Association\\\\Model\\\\User\\>\\:\\:getCollection\\(\\)$#',
	'identifier' => 'argument.templateType',
	'count' => 8,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/Repository/UserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'id\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'password\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'username\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:__unserialize\\(\\) has parameter \\$serialized with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:getAlternateEmail\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:getDaysBeforeMembershipExpiration\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:getDirectoryLevel\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:getEventLevel\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:getNearestOfficeLabel\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:getNeedsUpToDateMembership\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:getOfficeLevel\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:getRoles\\(\\) should return array\\<string\\> but returns array\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:getUserIdentifier\\(\\) should return non\\-empty\\-string but returns string\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:getWebsiteLevel\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:hasRole\\(\\) has parameter \\$role with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:setAlternateEmail\\(\\) has parameter \\$alternateEmail with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:setDirectoryLevel\\(\\) has parameter \\$level with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:setEventLevel\\(\\) has parameter \\$level with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:setNeedsUpToDateMembership\\(\\) has parameter \\$needsUpToDateMembership with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:setOfficeLevel\\(\\) has parameter \\$level with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:setRoles\\(\\) has parameter \\$roles with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\Model\\\\User\\:\\:setWebsiteLevel\\(\\) has parameter \\$level with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_diff expects an array of values castable to string, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_unique expects an array of values castable to string, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Model\\\\User\\:\\:\\$alternateEmail \\(string\\|null\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Model\\\\User\\:\\:\\$id \\(int\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Model\\\\User\\:\\:\\$lastSubscription \\(DateTimeImmutable\\|null\\) does not accept DateTimeImmutable\\|false\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Model\\\\User\\:\\:\\$needsUpToDateMembership \\(bool\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Model\\\\User\\:\\:\\$password \\(string\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Model\\\\User\\:\\:\\$roles type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Association\\\\Model\\\\User\\:\\:\\$username \\(string\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/Model/User.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\NotifiableInterface\\:\\:getEmail\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/NotifiableInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\NotifiableInterface\\:\\:getId\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/NotifiableInterface.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$email of class AppBundle\\\\Email\\\\Mailer\\\\MailUser constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/AbstractUserReminder.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$email of method AppBundle\\\\Association\\\\Model\\\\SubscriptionReminderLog\\:\\:setEmail\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/AbstractUserReminder.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$userId of method AppBundle\\\\Association\\\\Model\\\\SubscriptionReminderLog\\:\\:setUserId\\(\\) expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/AbstractUserReminder.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 4,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'Participation à l…\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'Speaker de l\\\\\'année \' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'ag\\-\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'jy\\-etais\\-\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'speaker\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between literal\\-string&non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between mixed and \'\\-01\\-01\' results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'code\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'id\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 7,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getBadge\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDate\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDateStart\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getIssuedAt\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getLabel\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPath\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTitle\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method isPresent\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\UserMembership\\\\BadgesComputer\\:\\:filterExistingBadges\\(\\) has parameter \\$badgesInfos with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\UserMembership\\\\BadgesComputer\\:\\:getBadges\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\UserMembership\\\\BadgesComputer\\:\\:getCompanyBadges\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\UserMembership\\\\BadgesComputer\\:\\:mapBadgesCodes\\(\\) has parameter \\$badgesInfos with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\UserMembership\\\\BadgesComputer\\:\\:sortBadgesInfos\\(\\) has parameter \\$badgesInfos with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\UserMembership\\\\BadgesComputer\\:\\:sortBadgesInfos\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_unique expects an array of values castable to string, list given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$callback of function usort expects callable\\(mixed, mixed\\)\\: int, Closure\\(array, array\\)\\: int\\<\\-1, 1\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/BadgesComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method diff\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/SeniorityComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/SeniorityComputer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Association\\\\UserMembership\\\\UserReminderFactory\\:\\:getReminder\\(\\) has parameter \\$class with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/UserReminderFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/UserReminderFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$user of method AppBundle\\\\Association\\\\UserMembership\\\\UserService\\:\\:resetPassword\\(\\) expects AppBundle\\\\Association\\\\Model\\\\User, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Association/UserMembership/UserService.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/AuditLog/Audit.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$route of method AppBundle\\\\AuditLog\\\\AuditLogRepository\\:\\:save\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/AuditLog/Audit.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/AuditLog/AuditLogRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/CFP/PhotoStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\CFP\\\\PhotoStorage\\:\\:getUrl\\(\\) has parameter \\$format with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/CFP/PhotoStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$files of method Symfony\\\\Component\\\\Filesystem\\\\Filesystem\\:\\:remove\\(\\) expects iterable\\|string, list\\<string\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/CFP/PhotoStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$image of function imagejpeg expects GdImage, GdImage\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/CFP/PhotoStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$image of function imagepng expects GdImage, GdImage\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/CFP/PhotoStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$image of function imagesx expects GdImage, GdImage\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/CFP/PhotoStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$image of function imagesy expects GdImage, GdImage\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/CFP/PhotoStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$width of function imagecreatetruecolor expects int\\<1, max\\>, int given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/CFP/PhotoStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$color of function imagecolortransparent expects int\\|null, int\\<0, max\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/CFP/PhotoStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$format of method AppBundle\\\\CFP\\\\PhotoStorage\\:\\:generateFormat\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/CFP/PhotoStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$format of method AppBundle\\\\CFP\\\\PhotoStorage\\:\\:getPath\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/CFP/PhotoStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$height of function imagecreatetruecolor expects int\\<1, max\\>, int\\<min, 1000\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/CFP/PhotoStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$src_image of function imagecopyresampled expects GdImage, GdImage\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/CFP/PhotoStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/CFP/PhotoStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventId of method AppBundle\\\\Event\\\\Model\\\\Speaker\\:\\:setEventId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/CFP/SpeakerFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$user of method AppBundle\\\\Event\\\\Model\\\\Speaker\\:\\:setUser\\(\\) expects int, int\\|Symfony\\\\Component\\\\Security\\\\Core\\\\User\\\\UserInterface\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/CFP/SpeakerFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type int\\|null\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/CFP/ViewModel/EventTalkList.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEnd\\(\\) on AppBundle\\\\Event\\\\Model\\\\Planning\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/IcsPlanningGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getName\\(\\) on AppBundle\\\\Event\\\\Model\\\\Room\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/IcsPlanningGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getStart\\(\\) on AppBundle\\\\Event\\\\Model\\\\Planning\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/IcsPlanningGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEnd\\(\\) on AppBundle\\\\Event\\\\Model\\\\Planning\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/JsonPlanningGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getName\\(\\) on AppBundle\\\\Event\\\\Model\\\\Room\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/JsonPlanningGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getStart\\(\\) on AppBundle\\\\Event\\\\Model\\\\Planning\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/JsonPlanningGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setTimezone\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/JsonPlanningGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/TechnoWatchCalendarGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/TechnoWatchCalendarGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'date\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/TechnoWatchCalendarGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'first_chair\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/TechnoWatchCalendarGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'second_chair\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/TechnoWatchCalendarGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/TechnoWatchCalendarGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/TechnoWatchCalendarGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Calendar\\\\TechnoWatchCalendarGenerator\\:\\:generate\\(\\) has parameter \\$displayPrefix with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/TechnoWatchCalendarGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Calendar\\\\TechnoWatchCalendarGenerator\\:\\:generate\\(\\) has parameter \\$filter with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/TechnoWatchCalendarGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Calendar\\\\TechnoWatchCalendarGenerator\\:\\:prepareEvents\\(\\) has parameter \\$filter with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/TechnoWatchCalendarGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Calendar\\\\TechnoWatchCalendarGenerator\\:\\:prepareEvents\\(\\) has parameter \\$googleSpreadsheetCsvUrl with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/TechnoWatchCalendarGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Calendar\\\\TechnoWatchCalendarGenerator\\:\\:prepareEvents\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/TechnoWatchCalendarGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of function fopen expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Calendar/TechnoWatchCalendarGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface\\<AppBundle\\\\Event\\\\Model\\\\Event\\>\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/CfpNotificationCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method doRun\\(\\) on Symfony\\\\Component\\\\Console\\\\Application\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/IndexMeetupsCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/PayboxCallbackSimulatorCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$cmd of method AppBundle\\\\Command\\\\PayboxCallbackSimulatorCommand\\:\\:callCotisation\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/PayboxCallbackSimulatorCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$cmd of method AppBundle\\\\Command\\\\PayboxCallbackSimulatorCommand\\:\\:callInvoice\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/PayboxCallbackSimulatorCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$amount of method AppBundle\\\\Command\\\\PayboxCallbackSimulatorCommand\\:\\:buildUrl\\(\\) expects float, float\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/PayboxCallbackSimulatorCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$status of method AppBundle\\\\Command\\\\PayboxCallbackSimulatorCommand\\:\\:callCotisation\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/PayboxCallbackSimulatorCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$status of method AppBundle\\\\Command\\\\PayboxCallbackSimulatorCommand\\:\\:callInvoice\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/PayboxCallbackSimulatorCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Part \\$cmd \\(mixed\\) of encapsed string cannot be cast to string\\.$#',
	'identifier' => 'encapsedStringPart.nonString',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/PayboxCallbackSimulatorCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Part \\$payementType \\(mixed\\) of encapsed string cannot be cast to string\\.$#',
	'identifier' => 'encapsedStringPart.nonString',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/PayboxCallbackSimulatorCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Part \\$status \\(mixed\\) of encapsed string cannot be cast to string\\.$#',
	'identifier' => 'encapsedStringPart.nonString',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/PayboxCallbackSimulatorCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Command/QrCodesGeneratorCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$idTicket of method AppBundle\\\\Event\\\\Ticket\\\\QrCodeGenerator\\:\\:generate\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/QrCodesGeneratorCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of class SplFileObject constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/RegistrationsExporterCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEmail\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/SubscriptionReminderCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Command\\\\SubscriptionReminderCommand\\:\\:handleReminders\\(\\) has parameter \\$dryRun with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/SubscriptionReminderCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Command\\\\SubscriptionReminderCommand\\:\\:handleReminders\\(\\) has parameter \\$users with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface but does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/SubscriptionReminderCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$messages of method Symfony\\\\Component\\\\Console\\\\Output\\\\OutputInterface\\:\\:writeln\\(\\) expects iterable\\<string\\>\\|string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/SubscriptionReminderCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$user of method AppBundle\\\\Association\\\\MembershipReminderInterface\\:\\:sendReminder\\(\\) expects AppBundle\\\\Association\\\\NotifiableInterface, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/SubscriptionReminderCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface\\<AppBundle\\\\Event\\\\Model\\\\Event\\>\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/TicketStatsNotificationCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/UpdateCompanyMemberStateCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/VideosDataCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getStart\\(\\) on AppBundle\\\\Event\\\\Model\\\\Planning\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/VideosDataCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$messages of method Symfony\\\\Component\\\\Console\\\\Output\\\\OutputInterface\\:\\:writeln\\(\\) expects iterable\\<string\\>\\|string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/VideosDataCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$path of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getByPath\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Command/VideosDataCommand.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$amount of method AppBundle\\\\Accounting\\\\Model\\\\Transaction\\:\\:setAmount\\(\\) expects float, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Compta/CsvExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$amountTva0 of method AppBundle\\\\Accounting\\\\Model\\\\Transaction\\:\\:setAmountTva0\\(\\) expects float\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Compta/CsvExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$amountTva10 of method AppBundle\\\\Accounting\\\\Model\\\\Transaction\\:\\:setAmountTva10\\(\\) expects float\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Compta/CsvExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$amountTva20 of method AppBundle\\\\Accounting\\\\Model\\\\Transaction\\:\\:setAmountTva20\\(\\) expects float\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Compta/CsvExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$amountTva5_5 of method AppBundle\\\\Accounting\\\\Model\\\\Transaction\\:\\:setAmountTva55\\(\\) expects float\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Compta/CsvExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$attachmentRequired of method AppBundle\\\\Accounting\\\\Model\\\\Transaction\\:\\:setAttachmentRequired\\(\\) expects bool, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Compta/CsvExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$categoryId of method AppBundle\\\\Accounting\\\\Model\\\\Transaction\\:\\:setCategoryId\\(\\) expects int\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Compta/CsvExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$datetime of class DateTime constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Compta/CsvExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$description of method AppBundle\\\\Accounting\\\\Model\\\\Transaction\\:\\:setDescription\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Compta/CsvExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventId of method AppBundle\\\\Accounting\\\\Model\\\\Transaction\\:\\:setEventId\\(\\) expects int\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Compta/CsvExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$operationId of method AppBundle\\\\Accounting\\\\Model\\\\Transaction\\:\\:setOperationId\\(\\) expects int\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Compta/CsvExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$paymentTypeId of method AppBundle\\\\Accounting\\\\Model\\\\Transaction\\:\\:setPaymentTypeId\\(\\) expects int\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Compta/CsvExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Compta\\\\Importer\\\\AutoQualifier\\:\\:qualify\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Compta/Importer/AutoQualifier.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type SplFileObject\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Compta/Importer/CreditMutuel.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method current\\(\\) on SplFileObject\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Compta/Importer/CreditMutuel.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method rewind\\(\\) on SplFileObject\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Compta/Importer/CreditMutuel.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Compta/Importer/CreditMutuel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of function count expects array\\|Countable, array\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Compta/Importer/CreditMutuel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function implode expects array\\<string\\>, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Compta/Importer/CreditMutuel.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\<string\\>\\|string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Compta/Importer/CreditMutuel.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$name on AppBundle\\\\Accounting\\\\Entity\\\\Account\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Configuration/EditAccountAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method AppBundle\\\\Doctrine\\\\EntityRepository\\<AppBundle\\\\Accounting\\\\Entity\\\\Account\\>\\:\\:save\\(\\) expects AppBundle\\\\Accounting\\\\Entity\\\\Account, AppBundle\\\\Accounting\\\\Entity\\\\Account\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Configuration/EditAccountAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$name on AppBundle\\\\Accounting\\\\Entity\\\\Category\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Configuration/EditCategoryAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method AppBundle\\\\Doctrine\\\\EntityRepository\\<AppBundle\\\\Accounting\\\\Entity\\\\Category\\>\\:\\:save\\(\\) expects AppBundle\\\\Accounting\\\\Entity\\\\Category, AppBundle\\\\Accounting\\\\Entity\\\\Category\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Configuration/EditCategoryAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$name on AppBundle\\\\Accounting\\\\Entity\\\\Event\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Configuration/EditEventAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method AppBundle\\\\Doctrine\\\\EntityRepository\\<AppBundle\\\\Accounting\\\\Entity\\\\Event\\>\\:\\:save\\(\\) expects AppBundle\\\\Accounting\\\\Entity\\\\Event, AppBundle\\\\Accounting\\\\Entity\\\\Event\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Configuration/EditEventAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$name on AppBundle\\\\Accounting\\\\Entity\\\\Operation\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Configuration/EditOperationAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method AppBundle\\\\Doctrine\\\\EntityRepository\\<AppBundle\\\\Accounting\\\\Entity\\\\Operation\\>\\:\\:save\\(\\) expects AppBundle\\\\Accounting\\\\Entity\\\\Operation, AppBundle\\\\Accounting\\\\Entity\\\\Operation\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Configuration/EditOperationAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$name on AppBundle\\\\Accounting\\\\Entity\\\\Payment\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Configuration/EditPaymentAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method AppBundle\\\\Doctrine\\\\EntityRepository\\<AppBundle\\\\Accounting\\\\Entity\\\\Payment\\>\\:\\:save\\(\\) expects AppBundle\\\\Accounting\\\\Entity\\\\Payment, AppBundle\\\\Accounting\\\\Entity\\\\Payment\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Configuration/EditPaymentAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$label on AppBundle\\\\Accounting\\\\Entity\\\\Rule\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Configuration/EditRuleAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method AppBundle\\\\Doctrine\\\\EntityRepository\\<AppBundle\\\\Accounting\\\\Entity\\\\Rule\\>\\:\\:save\\(\\) expects AppBundle\\\\Accounting\\\\Entity\\\\Rule, AppBundle\\\\Accounting\\\\Entity\\\\Rule\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Configuration/EditRuleAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$number of method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\InvoicingRepository\\:\\:getOneByInvoiceNumber\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Invoice/DownloadInvoiceAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$date of static method Afup\\\\Site\\\\Utils\\\\Vat\\:\\:isSubjectedToVat\\(\\) expects DateTimeInterface, DateTime\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Invoice/ListInvoiceAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$number of method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\InvoicingRepository\\:\\:getOneByInvoiceNumber\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Invoice/SendInvoiceEmailAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getAccountId\\(\\) on AppBundle\\\\Accounting\\\\Model\\\\Transaction\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/AllocateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getAccountingDate\\(\\) on AppBundle\\\\Accounting\\\\Model\\\\Transaction\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/AllocateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getAmount\\(\\) on AppBundle\\\\Accounting\\\\Model\\\\Transaction\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/AllocateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getComment\\(\\) on AppBundle\\\\Accounting\\\\Model\\\\Transaction\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/AllocateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDescription\\(\\) on AppBundle\\\\Accounting\\\\Model\\\\Transaction\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/AllocateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getNumber\\(\\) on AppBundle\\\\Accounting\\\\Model\\\\Transaction\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/AllocateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getOperationId\\(\\) on AppBundle\\\\Accounting\\\\Model\\\\Transaction\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/AllocateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getOperationNumber\\(\\) on AppBundle\\\\Accounting\\\\Model\\\\Transaction\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/AllocateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPaymentDate\\(\\) on AppBundle\\\\Accounting\\\\Model\\\\Transaction\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/AllocateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPaymentTypeId\\(\\) on AppBundle\\\\Accounting\\\\Model\\\\Transaction\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/AllocateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTvaIntra\\(\\) on AppBundle\\\\Accounting\\\\Model\\\\Transaction\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/AllocateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getVendorName\\(\\) on AppBundle\\\\Accounting\\\\Model\\\\Transaction\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/AllocateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setAmount\\(\\) on AppBundle\\\\Accounting\\\\Model\\\\Transaction\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/AllocateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Accounting\\\\Model\\\\Transaction\\>\\:\\:save\\(\\) expects AppBundle\\\\Accounting\\\\Model\\\\Transaction, AppBundle\\\\Accounting\\\\Model\\\\Transaction\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/AllocateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/DownloadAttachmentsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$start of class DatePeriod constructor expects DateTime, DateTime\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/DownloadAttachmentsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$end of class DatePeriod constructor expects DateTime, DateTime\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/DownloadAttachmentsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on AppBundle\\\\Accounting\\\\Model\\\\Transaction\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/EditTransactionAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$transactionId of method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\TransactionRepository\\:\\:getNextTransaction\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/EditTransactionAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'attachment_filename\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'attachment_required\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'categorie\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'comment\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'date_ecriture\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'description\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'evenement\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'idoperation\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'montant\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'montant_ht\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'montant_ht_0\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'montant_ht_10\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'montant_ht_20\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'montant_ht_5_5\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'montant_tva\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'montant_tva_10\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'montant_tva_20\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'montant_tva_5_5\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'nom_compte\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'reglement\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'tva_zone\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to float\\.$#',
	'identifier' => 'cast.double',
	'count' => 10,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$code of method AppBundle\\\\Compta\\\\Importer\\\\Factory\\:\\:create\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/ImportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/UploadAttachmentAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$content of class Symfony\\\\Component\\\\HttpFoundation\\\\Response constructor expects string\\|null, string\\|Stringable given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/UploadAttachmentAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function substr expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Journal/UploadAttachmentAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getCompanyName\\(\\) on AppBundle\\\\Association\\\\Model\\\\CompanyMember\\|AppBundle\\\\Association\\\\Model\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/MembershipFee/AddMembershipFeeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getFirstName\\(\\) on AppBundle\\\\Association\\\\Model\\\\CompanyMember\\|AppBundle\\\\Association\\\\Model\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/MembershipFee/AddMembershipFeeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on AppBundle\\\\Association\\\\Model\\\\CompanyMember\\|AppBundle\\\\Association\\\\Model\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/MembershipFee/AddMembershipFeeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getLastName\\(\\) on AppBundle\\\\Association\\\\Model\\\\CompanyMember\\|AppBundle\\\\Association\\\\Model\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/MembershipFee/AddMembershipFeeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$datetime of method Symfony\\\\Polyfill\\\\Intl\\\\Icu\\\\IntlDateFormatter\\:\\:format\\(\\) expects DateTimeInterface\\|int\\|string, DateTime\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/MembershipFee/AddMembershipFeeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\>\\:\\:delete\\(\\) expects AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee, AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/MembershipFee/DeleteMembershipFeeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$content of class Symfony\\\\Component\\\\HttpFoundation\\\\Response constructor expects string\\|null, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/MembershipFee/DownloadMembershipFeeInvoiceAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getCompanyName\\(\\) on AppBundle\\\\Association\\\\Model\\\\CompanyMember\\|AppBundle\\\\Association\\\\Model\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/MembershipFee/EditMembershipFeeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getFirstName\\(\\) on AppBundle\\\\Association\\\\Model\\\\CompanyMember\\|AppBundle\\\\Association\\\\Model\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/MembershipFee/EditMembershipFeeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on AppBundle\\\\Association\\\\Model\\\\CompanyMember\\|AppBundle\\\\Association\\\\Model\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/MembershipFee/EditMembershipFeeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getLastName\\(\\) on AppBundle\\\\Association\\\\Model\\\\CompanyMember\\|AppBundle\\\\Association\\\\Model\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/MembershipFee/EditMembershipFeeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\>\\:\\:save\\(\\) expects AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee, AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/MembershipFee/EditMembershipFeeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$number of method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\InvoicingRepository\\:\\:getOneByQuotationNumber\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Quotation/DownloadQuotationAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_diff expects an array of values castable to string, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Quotation/EditQuotationAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$ids of method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\InvoicingDetailRepository\\:\\:removeRowsPerIds\\(\\) expects array\\<int\\>, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Quotation/EditQuotationAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$invoicingId of method AppBundle\\\\Accounting\\\\Model\\\\Repository\\\\InvoicingDetailRepository\\:\\:getRowsIdsPerInvoicingId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Quotation/EditQuotationAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$date of static method Afup\\\\Site\\\\Utils\\\\Vat\\:\\:isSubjectedToVat\\(\\) expects DateTimeInterface, DateTime\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/Quotation/ListQuotationAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'query\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/SearchAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$query of method AppBundle\\\\Accounting\\\\SearchResultProvider\\:\\:getResultsForQuery\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Accounting/SearchAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method count\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/DeleteSponsorTicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/EventAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Event\\\\EventAction\\:\\:__invoke\\(\\) has parameter \\$id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/EventAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Event\\\\EventAction\\:\\:moveRegistrationEmailFile\\(\\) has parameter \\$form with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/EventAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Event\\\\EventAction\\:\\:moveSponsorFile\\(\\) has parameter \\$form with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/EventAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot clone DateTime\\|null\\.$#',
	'identifier' => 'clone.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/ExtendSpecialPriceAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$reference of method AppBundle\\\\Event\\\\Invoice\\\\EventInvoicePdfGenerator\\:\\:generateQuote\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Facturation/DownloadDevisAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$reference of method AppBundle\\\\Event\\\\Invoice\\\\EventInvoicePdfGenerator\\:\\:generateInvoice\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Facturation/DownloadFactureAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventId of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\InvoiceRepository\\:\\:getByEventId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Facturation/ListFacturesAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$reference of method AppBundle\\\\Event\\\\Invoice\\\\EventInvoiceMailer\\:\\:send\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Facturation/SendFactureAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$value of class Symfony\\\\Component\\\\Security\\\\Csrf\\\\CsrfToken constructor expects string\\|null, bool\\|float\\|int\\|string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/PendingBankwiresAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$fields of method SplFileObject\\:\\:fputcsv\\(\\) expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/PreviousRegistrationsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/PricesAddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$dateEnd of method AppBundle\\\\Event\\\\Model\\\\TicketEventType\\:\\:setDateEnd\\(\\) expects DateTime, DateTime\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/PricesAddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$dateStart of method AppBundle\\\\Event\\\\Model\\\\TicketEventType\\:\\:setDateStart\\(\\) expects DateTime, DateTime\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/PricesAddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventId of method AppBundle\\\\Event\\\\Model\\\\TicketEventType\\:\\:setEventId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/PricesAddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$message of class Symfony\\\\Component\\\\Form\\\\FormError constructor expects string, string\\|Stringable given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/PricesAddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/PricesEditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setTicketType\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketEventType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/PricesEditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$ticketEventType of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketEventTypeRepository\\:\\:update\\(\\) expects AppBundle\\\\Event\\\\Model\\\\TicketEventType, AppBundle\\\\Event\\\\Model\\\\TicketEventType\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/PricesEditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Event\\\\RedirectEventFromSessionListener\\:\\:__construct\\(\\) has parameter \\$controllersWithEventSelector with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/RedirectEventFromSessionListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$name of method Symfony\\\\Component\\\\Routing\\\\Generator\\\\UrlGeneratorInterface\\:\\:generate\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/RedirectEventFromSessionListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\.\\.\\.\\$arrays of function array_merge expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/RedirectEventFromSessionListener.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'est_supprimable\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/RemoveEventAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Event\\\\Model\\\\Event\\>\\:\\:delete\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Event, AppBundle\\\\Event\\\\Model\\\\Event\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/RemoveEventAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'edit_room_\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/RoomAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/RoomAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getName\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/RoomAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Event\\\\RoomAction\\:\\:getFormsForRooms\\(\\) has parameter \\$rooms with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface but does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/RoomAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Event\\\\RoomAction\\:\\:getFormsForRooms\\(\\) return type with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/RoomAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Event\\\\Model\\\\Room\\>\\:\\:delete\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Room, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/RoomAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Event\\\\Model\\\\Room\\>\\:\\:save\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Room, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/RoomAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventId of method AppBundle\\\\Event\\\\Model\\\\Room\\:\\:setEventId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/RoomAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/RoomAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/CalendarAjaxAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$datetime of class DateTime constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/CalendarAjaxAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'Suppression de la…\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/DeleteAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/DeleteAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEnd\\(\\) on AppBundle\\\\Event\\\\Model\\\\Planning\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on AppBundle\\\\Event\\\\Model\\\\Planning\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getStart\\(\\) on AppBundle\\\\Event\\\\Model\\\\Planning\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setRoomId\\(\\) on AppBundle\\\\Event\\\\Model\\\\Planning\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot clone DateTime\\|null\\.$#',
	'identifier' => 'clone.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Event\\\\Session\\\\EditAction\\:\\:getForm\\(\\) has parameter \\$roomChoices with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Event\\\\Session\\\\EditAction\\:\\:getForm\\(\\) return type with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Event\\\\Session\\\\EditAction\\:\\:roomChoices\\(\\) should return array\\<string, int\\> but returns array\\<int\\|null\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$data of method AppBundle\\\\Controller\\\\Admin\\\\Event\\\\Session\\\\EditAction\\:\\:getForm\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Planning, AppBundle\\\\Event\\\\Model\\\\Planning\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Event\\\\Model\\\\Planning\\>\\:\\:save\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Planning, AppBundle\\\\Event\\\\Model\\\\Planning\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventId of method AppBundle\\\\Event\\\\Model\\\\Planning\\:\\:setEventId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$talkId of method AppBundle\\\\Event\\\\Model\\\\Planning\\:\\:setTalkId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of class AppBundle\\\\Event\\\\Model\\\\Session\\\\CalendarEvent constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of class AppBundle\\\\Event\\\\Model\\\\Session\\\\CalendarResource constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$title of class AppBundle\\\\Event\\\\Model\\\\Session\\\\CalendarResource constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#5 \\$resourceId of class AppBundle\\\\Event\\\\Model\\\\Session\\\\CalendarEvent constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Session/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$speaker of method AppBundle\\\\Event\\\\Speaker\\\\SpeakerPage\\:\\:handleRequest\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Speaker, AppBundle\\\\Event\\\\Model\\\\Speaker\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/SpeakerInfosAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'hasExpensesFiles\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/SpeakersExpensesAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'speaker\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/SpeakersExpensesAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$speaker of method AppBundle\\\\SpeakerInfos\\\\SpeakersExpensesStorage\\:\\:getFiles\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Speaker, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/SpeakersExpensesAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'hasExpensesFiles\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/SpeakersManagementAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'speaker\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/SpeakersManagementAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$speaker of method AppBundle\\\\SpeakerInfos\\\\SpeakersExpensesStorage\\:\\:getFiles\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Speaker, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/SpeakersManagementAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Event\\\\Model\\\\TicketSpecialPrice\\>\\:\\:save\\(\\) expects AppBundle\\\\Event\\\\Model\\\\TicketSpecialPrice, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/SpecialPriceAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventId of method AppBundle\\\\Event\\\\Model\\\\TicketSpecialPrice\\:\\:setEventId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/SpecialPriceAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on AppBundle\\\\Event\\\\Model\\\\SponsorTicket\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/SponsorTicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setEditedOn\\(\\) on AppBundle\\\\Event\\\\Model\\\\SponsorTicket\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/SponsorTicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Event\\\\Model\\\\SponsorTicket\\>\\:\\:save\\(\\) expects AppBundle\\\\Event\\\\Model\\\\SponsorTicket, AppBundle\\\\Event\\\\Model\\\\SponsorTicket\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/SponsorTicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$idForum of method AppBundle\\\\Event\\\\Model\\\\SponsorTicket\\:\\:setIdForum\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/SponsorTicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$sponsorTicket of method AppBundle\\\\Event\\\\Ticket\\\\SponsorTokenMail\\:\\:sendNotification\\(\\) expects AppBundle\\\\Event\\\\Model\\\\SponsorTicket, AppBundle\\\\Event\\\\Model\\\\SponsorTicket\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/SponsorTicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'n\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/StatsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'n_1\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/StatsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPrettyName\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/StatsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventId of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventStatsRepository\\:\\:getRegistrationTracking\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/StatsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_map expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/StatsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getAmount\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getForumId\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getInvoiceStatus\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getLabel\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPaymentDate\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getStatus\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTicketTypeId\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setAmount\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setDate\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setForumId\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setInvoice\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setInvoiceDate\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setReference\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setStatus\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setTicketEventType\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot use array destructuring on mixed\\.$#',
	'identifier' => 'offsetAccess.nonArray',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Event\\\\Model\\\\Invoice\\>\\:\\:save\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Invoice, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Event\\\\Model\\\\Ticket\\>\\:\\:save\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Ticket, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventId of method AppBundle\\\\Event\\\\Invoice\\\\EventInvoiceReferenceGenerator\\:\\:generate\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$label of method AppBundle\\\\Event\\\\Invoice\\\\EventInvoiceReferenceGenerator\\:\\:generate\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getLabel\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getStatus\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setStatus\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot use array destructuring on mixed\\.$#',
	'identifier' => 'offsetAccess.nonArray',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Event\\\\Model\\\\Invoice\\>\\:\\:save\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Invoice, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Event\\\\Model\\\\Ticket\\>\\:\\:save\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Ticket, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Event\\\\Ticket\\\\IndexAction\\:\\:computeStatistics\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Event\\\\Ticket\\\\IndexAction\\:\\:filterForm\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Event\\\\Ticket\\\\IndexAction\\:\\:filterForm\\(\\) return type with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_filter expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventId of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventStatsRepository\\:\\:getStats\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\$search of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getByEventWithAll\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\$sortDirection of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getByEventWithAll\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\$sortKey of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getByEventWithAll\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/Ticket/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventId of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\VoteRepository\\:\\:getVotesByEvent\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Event/VotesListeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/GetMenuAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'elements\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/GetMenuAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'extra_pages\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/GetMenuAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'extra_routes\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/GetMenuAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/GetMenuAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getLogin\\(\\) on AppBundle\\\\Event\\\\Model\\\\GithubUser\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/GithubUser/GithubUserAddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setAfupCrew\\(\\) on AppBundle\\\\Event\\\\Model\\\\GithubUser\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/GithubUser/GithubUserAddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\GithubUser\\\\GithubUserAddAction\\:\\:__invoke\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/GithubUser/GithubUserAddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Event\\\\Model\\\\GithubUser\\>\\:\\:save\\(\\) expects AppBundle\\\\Event\\\\Model\\\\GithubUser, AppBundle\\\\Event\\\\Model\\\\GithubUser\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/GithubUser/GithubUserAddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of function count expects array\\|Countable, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/GithubUser/GithubUserListAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'CURRENT_TIMESTAMP\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/HealthcheckController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$datetime of class DateTime constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/HealthcheckController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'entrées \\(premier jour\\)\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/HomeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset non\\-falsy\\-string on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/HomeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$date of method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:countAttendees\\(\\) expects DateTimeInterface, DateTimeImmutable\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/HomeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$date of method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:countAttendeesAndPowers\\(\\) expects DateTimeInterface, DateTimeImmutable\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/HomeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$date of method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:obtenirEcartQuorum\\(\\) expects DateTimeInterface, DateTimeImmutable\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/HomeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type string\\|null\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/HomeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'image\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/BadgeNewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'label\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/BadgeNewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method move\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/BadgeNewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$label of method AppBundle\\\\Event\\\\Model\\\\Badge\\:\\:setLabel\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/BadgeNewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Members\\\\CompanyListAction\\:\\:__invoke\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/CompanyListAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'description\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$date of method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:save\\(\\) expects DateTimeInterface, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$description of method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:save\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$datetime of static method DateTimeImmutable\\:\\:createFromFormat\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/ListAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTimeImmutable\\|false\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/ListingAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$date of method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:getAttendees\\(\\) expects DateTimeInterface, DateTimeImmutable\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/ListingAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'date\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/PrepareAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'description\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/PrepareAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$date of method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:prepare\\(\\) expects DateTimeInterface, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/PrepareAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$description of method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:prepare\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/PrepareAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/ReportsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "/" between mixed and \\(float\\|int\\) results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/ReportsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/ReportsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Members\\\\GeneralMeeting\\\\ReportsAction\\:\\:buildForm\\(\\) return type with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/ReportsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Members\\\\GeneralMeeting\\\\ReportsAction\\:\\:humanFilesize\\(\\) has parameter \\$bytes with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/ReportsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function rsort expects list\\<string\\>, list\\<string\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/ReportsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of function filemtime expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/ReportsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of function filesize expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/ReportsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$path of function pathinfo expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/ReportsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$route of method Symfony\\\\Bundle\\\\FrameworkBundle\\\\Controller\\\\AbstractController\\:\\:redirectToRoute\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeeting/ReportsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTimeImmutable\\|false\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingQuestion/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTimeInterface\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingQuestion/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Members\\\\GeneralMeetingQuestion\\\\AddAction\\:\\:__invoke\\(\\) has parameter \\$date with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingQuestion/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$date of method AppBundle\\\\Association\\\\Model\\\\GeneralMeetingQuestion\\:\\:setDate\\(\\) expects DateTimeInterface, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingQuestion/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$date of method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:findOneByDate\\(\\) expects DateTimeInterface, DateTimeImmutable\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingQuestion/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$datetime of static method DateTimeImmutable\\:\\:createFromFormat\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingQuestion/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTimeInterface\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingQuestion/DeleteAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Members\\\\GeneralMeetingQuestion\\\\DeleteAction\\:\\:__invoke\\(\\) has parameter \\$id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingQuestion/DeleteAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingQuestion/DeleteAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTimeInterface\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingQuestion/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Members\\\\GeneralMeetingQuestion\\\\EditAction\\:\\:__invoke\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingQuestion/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Members\\\\GeneralMeetingQuestion\\\\EditAction\\:\\:__invoke\\(\\) has parameter \\$id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingQuestion/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingQuestion/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTimeInterface\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingVote/CloseAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$generalMeetingDate of method AppBundle\\\\Association\\\\Model\\\\Repository\\\\GeneralMeetingQuestionRepository\\:\\:loadByDate\\(\\) expects DateTimeInterface, DateTimeImmutable\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingVote/ListAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$datetime of static method DateTimeImmutable\\:\\:createFromFormat\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingVote/ListAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTimeInterface\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/GeneralMeetingVote/OpenAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Event\\\\Model\\\\UserBadge\\>\\:\\:delete\\(\\) expects AppBundle\\\\Event\\\\Model\\\\UserBadge, AppBundle\\\\Event\\\\Model\\\\UserBadge\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/UserBadgeDeleteAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$url of class Symfony\\\\Component\\\\HttpFoundation\\\\RedirectResponse constructor expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/UserBadgeDeleteAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'badge\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/UserBadgeNewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'date\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/UserBadgeNewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'user\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/UserBadgeNewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$badgeId of method AppBundle\\\\Event\\\\Model\\\\UserBadge\\:\\:setBadgeId\\(\\) expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/UserBadgeNewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$issuedAt of method AppBundle\\\\Event\\\\Model\\\\UserBadge\\:\\:setIssuedAt\\(\\) expects DateTime\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/UserBadgeNewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$url of class Symfony\\\\Component\\\\HttpFoundation\\\\RedirectResponse constructor expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/UserBadgeNewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$userId of method AppBundle\\\\Event\\\\Model\\\\UserBadge\\:\\:setUserId\\(\\) expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/UserBadgeNewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'first\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/UserEditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$iterator of function iterator_to_array expects iterable, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/UserEditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$plainPassword of method Symfony\\\\Component\\\\PasswordHasher\\\\Hasher\\\\UserPasswordHasherInterface\\:\\:hashPassword\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Members/UserEditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/AddRubriqueAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getClientOriginalName\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/AddRubriqueAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method guessExtension\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/AddRubriqueAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method move\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/AddRubriqueAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/AddRubriqueAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$nom on AppBundle\\\\Site\\\\Entity\\\\Rubrique\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/DeleteRubriqueAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method AppBundle\\\\Doctrine\\\\EntityRepository\\<AppBundle\\\\Site\\\\Entity\\\\Rubrique\\>\\:\\:delete\\(\\) expects AppBundle\\\\Site\\\\Entity\\\\Rubrique, AppBundle\\\\Site\\\\Entity\\\\Rubrique\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/DeleteRubriqueAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/EditRubriqueAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$icone on AppBundle\\\\Site\\\\Entity\\\\Rubrique\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/EditRubriqueAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$nom on AppBundle\\\\Site\\\\Entity\\\\Rubrique\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/EditRubriqueAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getClientOriginalName\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/EditRubriqueAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method guessExtension\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/EditRubriqueAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method move\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/EditRubriqueAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/EditRubriqueAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method AppBundle\\\\Doctrine\\\\EntityRepository\\<AppBundle\\\\Site\\\\Entity\\\\Rubrique\\>\\:\\:save\\(\\) expects AppBundle\\\\Site\\\\Entity\\\\Rubrique, AppBundle\\\\Site\\\\Entity\\\\Rubrique\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/EditRubriqueAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$nom on AppBundle\\\\Site\\\\Entity\\\\Feuille\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/Feuille/DeleteFeuilleAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method AppBundle\\\\Doctrine\\\\EntityRepository\\<AppBundle\\\\Site\\\\Entity\\\\Feuille\\>\\:\\:delete\\(\\) expects AppBundle\\\\Site\\\\Entity\\\\Feuille, AppBundle\\\\Site\\\\Entity\\\\Feuille\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/Feuille/DeleteFeuilleAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$image on AppBundle\\\\Site\\\\Entity\\\\Feuille\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/Feuille/EditFeuilleAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$nom on AppBundle\\\\Site\\\\Entity\\\\Feuille\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/Feuille/EditFeuilleAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method AppBundle\\\\Doctrine\\\\EntityRepository\\<AppBundle\\\\Site\\\\Entity\\\\Feuille\\>\\:\\:save\\(\\) expects AppBundle\\\\Site\\\\Entity\\\\Feuille, AppBundle\\\\Site\\\\Entity\\\\Feuille\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Site/Feuille/EditFeuilleAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on AppBundle\\\\Event\\\\Model\\\\GithubUser\\|int\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Speaker/SpeakerAddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventId of method AppBundle\\\\Event\\\\Model\\\\Speaker\\:\\:setEventId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Speaker/SpeakerAddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$user of method AppBundle\\\\Event\\\\Model\\\\Speaker\\:\\:setUser\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Speaker/SpeakerAddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on AppBundle\\\\Event\\\\Model\\\\GithubUser\\|int\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Speaker/SpeakerEditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Speaker\\\\SpeakerEditAction\\:\\:__invoke\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Speaker/SpeakerEditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$user of method AppBundle\\\\Event\\\\Model\\\\Speaker\\:\\:setUser\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Speaker/SpeakerEditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type int\\|null\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Speaker/SpeakerListAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$forumId of method AppBundle\\\\Event\\\\Model\\\\Ticket\\:\\:setForumId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Speaker/SpeakerRegisterAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$speakers of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkToSpeakersRepository\\:\\:replaceSpeakers\\(\\) expects array\\<AppBundle\\\\Event\\\\Model\\\\Speaker\\>, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Talk/AddAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$speakers of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkToSpeakersRepository\\:\\:replaceSpeakers\\(\\) expects array\\<AppBundle\\\\Event\\\\Model\\\\Speaker\\>, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Talk/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPath\\(\\) on AppBundle\\\\Event\\\\Model\\\\Event\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Talk/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$event of method AppBundle\\\\Event\\\\Talk\\\\ExportGenerator\\:\\:export\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Event, AppBundle\\\\Event\\\\Model\\\\Event\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Talk/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPath\\(\\) on AppBundle\\\\Event\\\\Model\\\\Event\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Talk/ExportJoindInAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$event of method AppBundle\\\\Event\\\\Talk\\\\ExportGenerator\\:\\:exportJoindIn\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Event, AppBundle\\\\Event\\\\Model\\\\Event\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Talk/ExportJoindInAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between mixed and \' \' results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Talk/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Talk/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Talk\\\\IndexAction\\:\\:filterForm\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Talk/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\Talk\\\\IndexAction\\:\\:filterForm\\(\\) return type with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Talk/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_filter expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Talk/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\$needMentoring of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:getByEventWithSpeakersAndVotes\\(\\) expects bool, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Talk/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\$planned of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:getByEventWithSpeakersAndVotes\\(\\) expects bool, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Talk/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\$search of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:getByEventWithSpeakersAndVotes\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/Talk/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'La campagne a été…\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/GenerateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'id\' on array\\|bool\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/GenerateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$detail on mixed\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/GenerateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$title on mixed\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/GenerateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Admin\\\\TechLetter\\\\GenerateAction\\:\\:__invoke\\(\\) has parameter \\$techletterId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/GenerateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$campaignId of method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:scheduleCampaign\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/GenerateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$datetime of class DateTime constructor expects string, bool\\|float\\|int\\|string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/GenerateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$html of method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:createTemplate\\(\\) expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/GenerateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$token of method Symfony\\\\Bundle\\\\FrameworkBundle\\\\Controller\\\\AbstractController\\:\\:isCsrfTokenValid\\(\\) expects string\\|null, bool\\|float\\|int\\|string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 4,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/GenerateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/GenerateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/GenerateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Veille\\\\Entity\\\\Envoi\\:\\:\\$urlArchive \\(string\\|null\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/GenerateAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$id on mixed\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method AppBundle\\\\Doctrine\\\\EntityRepository\\<AppBundle\\\\Veille\\\\Entity\\\\Envoi\\>\\:\\:save\\(\\) expects AppBundle\\\\Veille\\\\Entity\\\\Envoi, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$json of method AppBundle\\\\TechLetter\\\\Model\\\\TechLetterFactory\\:\\:createTechLetterFromJson\\(\\) expects string\\|null, bool\\|float\\|int\\|string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/PreviewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$token of method Symfony\\\\Bundle\\\\FrameworkBundle\\\\Controller\\\\AbstractController\\:\\:isCsrfTokenValid\\(\\) expects string\\|null, bool\\|float\\|int\\|string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/PreviewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Veille\\\\Entity\\\\Envoi\\:\\:\\$contenu \\(string\\|null\\) does not accept string\\|false\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Admin/TechLetter/PreviewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$urlName on AppBundle\\\\Antennes\\\\Meetup\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Api/Antennes/GetOneAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getIterator\\(\\) on CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Api/Antennes/GetOneAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Api\\\\Antennes\\\\GetOneAction\\:\\:transformMeetup\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Api/Antennes/GetOneAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(mixed\\)\\: mixed\\)\\|null, Closure\\(AppBundle\\\\Event\\\\Model\\\\Meetup\\)\\: array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Api/Antennes/GetOneAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'email\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Auth/LostPasswordAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$email of method AppBundle\\\\Association\\\\UserMembership\\\\UserService\\:\\:resetPasswordForEmail\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Auth/LostPasswordAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method diff\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Blog/PlanningAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Blog/PlanningAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on AppBundle\\\\Event\\\\Model\\\\Room\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Blog/PlanningAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setTimezone\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Blog/PlanningAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$events of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:getByEventsWithSpeakers\\(\\) expects list\\<AppBundle\\\\Event\\\\Model\\\\Event\\>, non\\-empty\\-array\\<\'\'\\|int, AppBundle\\\\Event\\\\Model\\\\Event\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Blog/PlanningAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type int\\|null\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 6,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Blog/PlanningAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Event\\\\Blog\\\\ProgramAction\\:\\:__invoke\\(\\) has parameter \\$eventSlug with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Blog/ProgramAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventSlug of method AppBundle\\\\Controller\\\\Event\\\\EventActionHelper\\:\\:getEvent\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Blog/ProgramAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$object of static method DateTimeImmutable\\:\\:createFromMutable\\(\\) expects DateTime, DateTime\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Blog/ProgramAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Blog/TalkWidgetAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'\\.aggregation\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Blog/TalkWidgetAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'speaker\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Blog/TalkWidgetAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Blog/TalkWidgetAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Blog/TalkWidgetAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Blog/TalkWidgetAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/CFP/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Event\\\\CFP\\\\EditAction\\:\\:createInvitationForm\\(\\) return type with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/CFP/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventSlug of method AppBundle\\\\Controller\\\\Event\\\\EventActionHelper\\:\\:getEvent\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/CFP/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$submittedBy of method AppBundle\\\\Event\\\\Model\\\\TalkInvitation\\:\\:setSubmittedBy\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/CFP/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$talkId of method AppBundle\\\\Event\\\\Model\\\\TalkInvitation\\:\\:setTalkId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/CFP/EditAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventSlug of method AppBundle\\\\Controller\\\\Event\\\\EventActionHelper\\:\\:getEvent\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/CFP/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type int\\|null\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/CFP/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/CFP/InviteAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventSlug of method AppBundle\\\\Controller\\\\Event\\\\EventActionHelper\\:\\:getEvent\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/CFP/InviteAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventSlug of method AppBundle\\\\Controller\\\\Event\\\\EventActionHelper\\:\\:getEvent\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/CFP/ProposeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventSlug of method AppBundle\\\\Controller\\\\Event\\\\EventActionHelper\\:\\:getEvent\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/CFP/SpeakerAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$parameters of method Symfony\\\\Bundle\\\\FrameworkBundle\\\\Controller\\\\AbstractController\\:\\:redirectToRoute\\(\\) expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/CFP/SpeakerAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$values of method Symfony\\\\Component\\\\HttpFoundation\\\\ResponseHeaderBag\\:\\:set\\(\\) expects array\\<string\\>\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Event/OpenFeedbackJsonAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'talks\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Event/ShowAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'votes\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Event/ShowAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPath\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Event/SpeakerInfosIndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$query on Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/EventActionHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getSession\\(\\) on Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/EventActionHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$event of class AppBundle\\\\Event\\\\AdminEventSelection constructor expects AppBundle\\\\Event\\\\Model\\\\Event, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/EventActionHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Part \\$selectedEventId \\(mixed\\) of encapsed string cannot be cast to string\\.$#',
	'identifier' => 'encapsedStringPart.nonString',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/EventActionHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPath\\(\\) on AppBundle\\\\Event\\\\Model\\\\Event\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Lead/BecomeSponsorLatestAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Event\\\\Lead\\\\PostLeadAction\\:\\:__invoke\\(\\) has parameter \\$eventSlug with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Lead/PostLeadAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventSlug of method AppBundle\\\\Controller\\\\Event\\\\EventActionHelper\\:\\:getEvent\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Lead/PostLeadAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEventId\\(\\) on AppBundle\\\\Event\\\\Model\\\\Speaker\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Speaker/FilesAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventSlug of method AppBundle\\\\Controller\\\\Event\\\\EventActionHelper\\:\\:getEvent\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Speaker/FilesAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$speaker of method AppBundle\\\\SpeakerInfos\\\\SpeakersExpensesStorage\\:\\:getFiles\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Speaker, AppBundle\\\\Event\\\\Model\\\\Speaker\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Speaker/FilesAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Speaker/FilesAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventSlug of method AppBundle\\\\Controller\\\\Event\\\\EventActionHelper\\:\\:getEvent\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Speaker/PageAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Event\\\\Speaker\\\\SuggestionAction\\:\\:createSpeakerSuggestion\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Speaker/SuggestionAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$comment of method AppBundle\\\\Event\\\\Model\\\\SpeakerSuggestion\\:\\:setComment\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Speaker/SuggestionAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventId of method AppBundle\\\\Event\\\\Model\\\\SpeakerSuggestion\\:\\:setEventId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Speaker/SuggestionAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$speakerName of method AppBundle\\\\Event\\\\Model\\\\SpeakerSuggestion\\:\\:setSpeakerName\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Speaker/SuggestionAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$suggesterEmail of method AppBundle\\\\Event\\\\Model\\\\SpeakerSuggestion\\:\\:setSuggesterEmail\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Speaker/SuggestionAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$suggesterName of method AppBundle\\\\Event\\\\Model\\\\SpeakerSuggestion\\:\\:setSuggesterName\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Speaker/SuggestionAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$data of method AppBundle\\\\Controller\\\\Event\\\\Speaker\\\\SuggestionAction\\:\\:createSpeakerSuggestion\\(\\) expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Speaker/SuggestionAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/SponsorScan/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'created_on\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/SponsorScan/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'email\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/SponsorScan/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'nom\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/SponsorScan/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'prenom\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/SponsorScan/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/SponsorScan/ExportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$sponsorTicketId of method AppBundle\\\\Event\\\\Model\\\\SponsorScan\\:\\:setSponsorTicketId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/SponsorScan/FlashAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$ticketId of method AppBundle\\\\Event\\\\Model\\\\SponsorScan\\:\\:setTicketId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/SponsorScan/FlashAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'code\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/SponsorScan/NewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/PayboxCallbackAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/PayboxRedirectAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/PaymentAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Event\\\\Ticket\\\\PaymentAction\\:\\:__invoke\\(\\) has parameter \\$eventSlug with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/PaymentAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventSlug of method AppBundle\\\\Controller\\\\Event\\\\EventActionHelper\\:\\:getEvent\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/PaymentAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$reference of method AppBundle\\\\Event\\\\Invoice\\\\EventInvoiceMailer\\:\\:send\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/PaymentAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/PaymentAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Event\\\\Ticket\\\\SponsorTicketAction\\:\\:__invoke\\(\\) has parameter \\$eventSlug with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/SponsorTicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventSlug of method AppBundle\\\\Controller\\\\Event\\\\EventActionHelper\\:\\:getEvent\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/SponsorTicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$token of method Symfony\\\\Bundle\\\\FrameworkBundle\\\\Controller\\\\AbstractController\\:\\:isCsrfTokenValid\\(\\) expects string\\|null, bool\\|float\\|int\\|string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/SponsorTicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Event\\\\Ticket\\\\SponsorTicketFormAction\\:\\:__invoke\\(\\) has parameter \\$eventSlug with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/SponsorTicketFormAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventSlug of method AppBundle\\\\Controller\\\\Event\\\\EventActionHelper\\:\\:getEvent\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/SponsorTicketFormAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method createView\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/TicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method get\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 4,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/TicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getData\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 6,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/TicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getIsRestrictedToMembers\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/TicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTicketType\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketEventType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/TicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTickets\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/TicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method handleRequest\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/TicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method isSubmitted\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/TicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method isValid\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/TicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Event\\\\Ticket\\\\TicketAction\\:\\:__invoke\\(\\) has parameter \\$eventSlug with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/TicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$companyCitation of method AppBundle\\\\Event\\\\Model\\\\Ticket\\:\\:setCompanyCitation\\(\\) expects bool, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/TicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventId of method AppBundle\\\\Event\\\\Invoice\\\\EventInvoiceReferenceGenerator\\:\\:generate\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/TicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventSlug of method AppBundle\\\\Controller\\\\Event\\\\EventActionHelper\\:\\:getEvent\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/TicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$newsletter of method AppBundle\\\\Event\\\\Model\\\\Ticket\\:\\:setNewsletter\\(\\) expects bool, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/TicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of function count expects array\\|Countable, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/TicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$length of function array_slice expects int\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Ticket/TicketAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'asvg\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Vote/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'sessions\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Vote/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$talkId of method AppBundle\\\\Controller\\\\Event\\\\Vote\\\\VoteController\\:\\:createVoteForm\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Vote/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$vote of method AppBundle\\\\Controller\\\\Event\\\\Vote\\\\VoteController\\:\\:createVoteForm\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Vote, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Vote/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setSubmittedOn\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Vote/NewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setTalk\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Vote/NewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$user of method AppBundle\\\\Event\\\\Model\\\\Vote\\:\\:setUser\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Vote/NewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$vote of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\VoteRepository\\:\\:upsert\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Vote, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Vote/NewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$vote of method AppBundle\\\\Notifier\\\\SlackNotifier\\:\\:notifyVote\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Vote, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Vote/NewAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Event\\\\Vote\\\\VoteController\\:\\:createVoteForm\\(\\) return type with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Event/Vote/VoteController.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between literal\\-string&non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/LegacyController.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between mixed and \'\\.html\' results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/LegacyController.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between mixed and \'\\.js\\.html\' results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/LegacyController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'erreur\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/LegacyController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'message\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/LegacyController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method fetch\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/LegacyController.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method templateExists\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/LegacyController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\LegacyController\\:\\:__construct\\(\\) has parameter \\$backOfficePages with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/LegacyController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\LegacyController\\:\\:backOffice\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/LegacyController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\LegacyController\\:\\:void\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/LegacyController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$page of method Afup\\\\Site\\\\Droits\\:\\:verifierDroitSurLaPage\\(\\) expects int\\|string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/LegacyController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$content of class Symfony\\\\Component\\\\HttpFoundation\\\\Response constructor expects string\\|null, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Planete/ArticlesController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$content of class Symfony\\\\Component\\\\HttpFoundation\\\\Response constructor expects string\\|null, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Planete/FeedsController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\ValueResolver\\\\AdminEventSelectionValueResolver\\:\\:resolve\\(\\) return type has no value type specified in iterable type iterable\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/ValueResolver/AdminEventSelectionValueResolver.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$code of method AppBundle\\\\Antennes\\\\AntenneRepository\\:\\:findByCode\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/CompanyPublicProfile/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$callback of function usort expects callable\\(mixed, mixed\\)\\: int, Closure\\(AppBundle\\\\Association\\\\Model\\\\CompanyMember, AppBundle\\\\Association\\\\Model\\\\CompanyMember\\)\\: int\\<\\-1, 1\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/CompanyPublicProfile/ListAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'hits\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Global/HomeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method search\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Global/HomeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Website\\\\Global\\\\HomeAction\\:\\:doGetLatestMeetups\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Global/HomeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Website\\\\Global\\\\HomeAction\\:\\:getLatestMeetups\\(\\) should return array\\<AppBundle\\\\Event\\\\Model\\\\Meetup\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Global/HomeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Website\\\\Global\\\\HomeAction\\:\\:getTalkOfTheDay\\(\\) should return AppBundle\\\\Event\\\\Model\\\\Talk but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Global/HomeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Global/HtmlSitemapAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getCompanyName\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Global/HtmlSitemapAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Global/HtmlSitemapAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getSlug\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Global/HtmlSitemapAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Website\\\\Global\\\\HtmlSitemapAction\\:\\:buildPages\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Global/HtmlSitemapAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Website\\\\Global\\\\HtmlSitemapAction\\:\\:members\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Global/HtmlSitemapAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Website\\\\Global\\\\HtmlSitemapAction\\:\\:news\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Global/HtmlSitemapAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Website\\\\Global\\\\HtmlSitemapAction\\:\\:talks\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Global/HtmlSitemapAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'careers_page_url\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'contact_page_url\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'description\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'enabled\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'logo\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'membership_reason\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'related_afup_offices\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'twitter_handle\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'website_url\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$careersPageUrl of method AppBundle\\\\Association\\\\Model\\\\CompanyMember\\:\\:setCareersPageUrl\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$contactPageUrl of method AppBundle\\\\Association\\\\Model\\\\CompanyMember\\:\\:setContactPageUrl\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$description of method AppBundle\\\\Association\\\\Model\\\\CompanyMember\\:\\:setDescription\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$membershipReason of method AppBundle\\\\Association\\\\Model\\\\CompanyMember\\:\\:setMembershipReason\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$publicProfileEnabled of method AppBundle\\\\Association\\\\Model\\\\CompanyMember\\:\\:setPublicProfileEnabled\\(\\) expects bool, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$relatedAfupOffices of method AppBundle\\\\Association\\\\Model\\\\CompanyMember\\:\\:setFormattedRelatedAfupOffices\\(\\) expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$twitterHandle of method AppBundle\\\\Association\\\\Model\\\\CompanyMember\\:\\:setTwitterHandle\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$websiteUrl of method AppBundle\\\\Association\\\\Model\\\\CompanyMember\\:\\:setWebsiteUrl\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/CompanyPublicProfileAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of function count expects array\\|Countable, iterable\\<AppBundle\\\\Association\\\\Model\\\\GeneralMeetingQuestion\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\+" between int\\<0, max\\> and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/MembersAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method count\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/MembersAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Website\\\\Member\\\\MembersAction\\:\\:addUser\\(\\) has parameter \\$pendingInvitations with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface but does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/MembersAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Website\\\\Member\\\\MembersAction\\:\\:addUser\\(\\) has parameter \\$users with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface but does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/MembersAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$users of method AppBundle\\\\Controller\\\\Website\\\\Member\\\\MembersAction\\:\\:disproveUser\\(\\) expects CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface\\<AppBundle\\\\Association\\\\Model\\\\User\\>, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/MembersAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$users of method AppBundle\\\\Controller\\\\Website\\\\Member\\\\MembersAction\\:\\:promoteUser\\(\\) expects CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface\\<AppBundle\\\\Association\\\\Model\\\\User\\>, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/MembersAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$users of method AppBundle\\\\Controller\\\\Website\\\\Member\\\\MembersAction\\:\\:removeUser\\(\\) expects CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface\\<AppBundle\\\\Association\\\\Model\\\\User\\>, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/MembersAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$value of class Symfony\\\\Component\\\\Security\\\\Csrf\\\\CsrfToken constructor expects string\\|null, bool\\|float\\|int\\|string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/MembersAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$users of method AppBundle\\\\Controller\\\\Website\\\\Member\\\\MembersAction\\:\\:addUser\\(\\) expects CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Member/MembersAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\<AppBundle\\\\Association\\\\Model\\\\CompanyMemberInvitation\\>\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/CompanyAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of function count expects array\\|Countable, array\\<AppBundle\\\\Association\\\\Model\\\\CompanyMemberInvitation\\>\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/CompanyAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$numberOfMembers of method AppBundle\\\\Association\\\\CompanyMembership\\\\SubscriptionManagement\\:\\:createInvoiceForInscription\\(\\) expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/CompanyAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'first\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/ContactDetailsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$plainPassword of method Symfony\\\\Component\\\\PasswordHasher\\\\Hasher\\\\UserPasswordHasherInterface\\:\\:hashPassword\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/ContactDetailsAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/Fee/DownloadAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getStartDate\\(\\) on AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/Fee/DownloadAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getUserId\\(\\) on AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/Fee/DownloadAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getUserType\\(\\) on AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/Fee/DownloadAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/Fee/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$idPersonne of method AppBundle\\\\Association\\\\MembershipFeeReferenceGenerator\\:\\:generate\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/Fee/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Website\\\\Membership\\\\GeneralMeeting\\\\DownloadReportAction\\:\\:__invoke\\(\\) has parameter \\$filename with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/GeneralMeeting/DownloadReportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$file of class Symfony\\\\Component\\\\HttpFoundation\\\\BinaryFileResponse constructor expects SplFileInfo\\|string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/GeneralMeeting/DownloadReportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/GeneralMeeting/DownloadReportAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'id_personne_avec_pouvoir\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/GeneralMeeting/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'presence\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/GeneralMeeting/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEndDate\\(\\) on AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/GeneralMeeting/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/GeneralMeeting/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTimestamp\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/GeneralMeeting/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/GeneralMeeting/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$presence of method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:addAttendee\\(\\) expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/GeneralMeeting/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$presence of method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:editAttendee\\(\\) expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/GeneralMeeting/IndexAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$date of method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:getAttendees\\(\\) expects DateTimeInterface, DateTimeInterface\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/GeneralMeeting/VoteAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$userId of method AppBundle\\\\Association\\\\Model\\\\GeneralMeetingVote\\:\\:setUserId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/GeneralMeeting/VoteAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$content of class Symfony\\\\Component\\\\HttpFoundation\\\\Response constructor expects string\\|null, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/InvoiceAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$idCotisation of method AppBundle\\\\MembershipFee\\\\MembershipFeeInvoicePdfGenerator\\:\\:genererFacture\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/InvoiceAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$plainPassword of method Symfony\\\\Component\\\\PasswordHasher\\\\Hasher\\\\UserPasswordHasherInterface\\:\\:hashPassword\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/MemberInvitationAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$user of class AppBundle\\\\Association\\\\Event\\\\NewMemberEvent constructor expects AppBundle\\\\Association\\\\Model\\\\User, AppBundle\\\\Association\\\\Model\\\\User\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/PayboxCallbackAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTimestamp\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/PaymentAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Website\\\\Membership\\\\RegisterAction\\:\\:__invoke\\(\\) should return Symfony\\\\Component\\\\HttpFoundation\\\\Response but returns Symfony\\\\Component\\\\HttpFoundation\\\\Response\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/RegisterAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$plainPassword of method Symfony\\\\Component\\\\PasswordHasher\\\\Hasher\\\\UserPasswordHasherInterface\\:\\:hashPassword\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/RegisterAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$token of method Symfony\\\\Bundle\\\\FrameworkBundle\\\\Controller\\\\AbstractController\\:\\:isCsrfTokenValid\\(\\) expects string\\|null, bool\\|float\\|int\\|string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Membership/Techletter/SubscribeAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filtres of method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\ArticleRepository\\:\\:countPublishedArticles\\(\\) expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/News/ListAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$filtres of method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\ArticleRepository\\:\\:findPublishedArticles\\(\\) expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/News/ListAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'email\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/NewsletterController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Website\\\\NewsletterController\\:\\:getSubscriberType\\(\\) return type with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/NewsletterController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$email of method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:subscribeAddress\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/NewsletterController.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\-" between mixed and 3 results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/PagerController.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "/" between mixed and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/PagerController.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Controller\\\\Website\\\\Paybox\\\\RedirectAction\\:\\:__invoke\\(\\) has parameter \\$type with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Paybox/RedirectAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/RssFeedController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$masterRequest of method AppBundle\\\\Controller\\\\Website\\\\SecondaryMenuController\\:\\:prepareMenu\\(\\) expects Symfony\\\\Component\\\\HttpFoundation\\\\Request, Symfony\\\\Component\\\\HttpFoundation\\\\Request\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/SecondaryMenuController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$parentId of method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\FeuilleRepository\\:\\:getFeuillesEnfant\\(\\) expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/SecondaryMenuController.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$parameters of method AppBundle\\\\Twig\\\\ViewRenderer\\:\\:render\\(\\) expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Static/VoidAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'https\\://joind\\.in…\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Talks/JoindinAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'Les vidéos de \' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Talks/ListAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between mixed and \' les vidéos\' results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Talks/ListAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'event\\.title\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Talks/ListAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'speakers\\.label\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Talks/ListAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset 0 on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Talks/ListAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEventId\\(\\) on AppBundle\\\\Event\\\\Model\\\\Planning\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Talks/ShowAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$data of method AppBundle\\\\Veille\\\\Entity\\\\Repository\\\\NewsletterDesinscriptionRepository\\:\\:createFromWebhookData\\(\\) expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Controller/Website/Techletter/WebhookAction.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$objectOrClass of class ReflectionClass constructor expects class\\-string\\<T of object\\>\\|T of object, string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/DependencyInjection/ControllersWithEventSelectorPass.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Doctrine/Type/UnixTimestampType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method add\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Email/Emails.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Email/Emails.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$filename of function unlink expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Email/Emails.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Email\\\\Emails\\:\\:\\$tempFiles type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Email/Emails.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Email\\\\Mailer\\\\Adapter\\\\MailerAdapter\\:\\:send\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Email/Mailer/Adapter/MailerAdapter.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\<AppBundle\\\\Email\\\\Mailer\\\\MailUser\\>\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Email/Mailer/Adapter/PhpMailerAdapter.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Email/Mailer/Adapter/PhpMailerAdapter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$smtpServer of class AppBundle\\\\Email\\\\Mailer\\\\Adapter\\\\PhpMailerAdapter constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Email/Mailer/Adapter/PhpMailerAdapter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$username of class AppBundle\\\\Email\\\\Mailer\\\\Adapter\\\\PhpMailerAdapter constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Email/Mailer/Adapter/PhpMailerAdapter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#4 \\$password of class AppBundle\\\\Email\\\\Mailer\\\\Adapter\\\\PhpMailerAdapter constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Email/Mailer/Adapter/PhpMailerAdapter.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type array\\<AppBundle\\\\Email\\\\Mailer\\\\MailUser\\>\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Email/Mailer/Adapter/SymfonyMailerAdapter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Email\\\\Mailer\\\\Mailer\\:\\:renderTemplate\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Email/Mailer/Mailer.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\EventCFPTextType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/EventCFPTextType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'event_id\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/EventCompareSelectType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\EventCompareSelectType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/EventCompareSelectType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Form\\\\EventCompareSelectType\\:\\:buildChoices\\(\\) should return array\\<string, int\\> but returns array\\<string, int\\|null\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/EventCompareSelectType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$eventCollection of method AppBundle\\\\Event\\\\Form\\\\EventCompareSelectType\\:\\:buildChoices\\(\\) expects CCMBenchmark\\\\Ting\\\\Repository\\\\Collection\\<AppBundle\\\\Event\\\\Model\\\\Event\\>, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/EventCompareSelectType.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type string\\|null\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/EventCompareSelectType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\EventSelectType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/EventSelectType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$events of method AppBundle\\\\Event\\\\Form\\\\Support\\\\EventHelper\\:\\:sortEventsByStartDate\\(\\) expects array\\<AppBundle\\\\Event\\\\Model\\\\Event\\>, array\\<int, mixed\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/EventSelectType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\EventType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/EventType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getLogin\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/GithubUserType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\GithubUserType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/GithubUserType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$username of method AppBundle\\\\Github\\\\GithubClient\\:\\:getUserInfos\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/GithubUserType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$repository of class AppBundle\\\\Validator\\\\Constraints\\\\UniqueEntity constructor expects CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\|string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/GithubUserType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\LeadType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/LeadType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\PurchaseType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/PurchaseType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\RoomType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/RoomType.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Event\\\\Form\\\\SpeakerFormData\\:\\:\\$mastodon \\(string\\) does not accept string\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/SpeakerFormDataFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Event\\\\Form\\\\SpeakerFormData\\:\\:\\$phoneNumber \\(string\\) does not accept string\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/SpeakerFormDataFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Event\\\\Form\\\\SpeakerFormData\\:\\:\\$referentPerson \\(string\\) does not accept string\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/SpeakerFormDataFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Event\\\\Form\\\\SpeakerFormData\\:\\:\\$referentPersonEmail \\(string\\) does not accept string\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/SpeakerFormDataFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\SpeakerSuggestionType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/SpeakerSuggestionType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\SpeakerType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/SpeakerType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$mastodon of method AppBundle\\\\Event\\\\Model\\\\Speaker\\:\\:setMastodon\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/SpeakerType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\SponsorScanType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/SponsorScanType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\SponsorTicketType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/SponsorTicketType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\SponsorTokenType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/SponsorTokenType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Form\\\\Support\\\\EventSelectFactory\\:\\:create\\(\\) return type with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/Support/EventSelectFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$event of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SpeakerRepository\\:\\:searchSpeakers\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Event, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TalkAdminType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$talk of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SpeakerRepository\\:\\:getSpeakersByTalk\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Talk, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TalkAdminType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\TalkInvitationType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TalkInvitationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\TalkType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TalkType.php',
];
$ignoreErrors[] = [
	'message' => '#^Anonymous function should return AppBundle\\\\Event\\\\Model\\\\TicketOffer\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketAdminType.php',
];
$ignoreErrors[] = [
	'message' => '#^Anonymous function should return int but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketAdminType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$ticketTypeId on mixed\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketAdminType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTransportInformationEnabled\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketAdminType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\TicketAdminType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketAdminType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function usort expects TArray of array\\<AppBundle\\\\Event\\\\Model\\\\TicketOffer\\>, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketAdminType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'invoice\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketAdminWithInvoiceType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'ticket\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketAdminWithInvoiceType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\TicketAdminWithInvoiceType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketAdminWithInvoiceType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\TicketInvoiceType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketInvoiceType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\TicketSpecialPriceType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketSpecialPriceType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getIsRestrictedToCfpSubmitter\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getIsRestrictedToMembers\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPrettyName\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTransportInformationEnabled\\(\\) on AppBundle\\\\Event\\\\Model\\\\Event\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\TicketType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$dateEnd of method AppBundle\\\\Event\\\\Model\\\\TicketEventType\\:\\:setDateEnd\\(\\) expects DateTime, DateTime\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$dateStart of method AppBundle\\\\Event\\\\Model\\\\TicketEventType\\:\\:setDateStart\\(\\) expects DateTime, DateTime\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$event of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketEventTypeRepository\\:\\:getTicketsByEvent\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Event, AppBundle\\\\Event\\\\Model\\\\Event\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$event of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketSpecialPriceRepository\\:\\:findUnusedToken\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Event, AppBundle\\\\Event\\\\Model\\\\Event\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$ticketType of method AppBundle\\\\Event\\\\Model\\\\TicketEventType\\:\\:setTicketType\\(\\) expects AppBundle\\\\Event\\\\Model\\\\TicketType, AppBundle\\\\Event\\\\Model\\\\TicketType\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$event of method AppBundle\\\\Event\\\\Ticket\\\\TicketTypeAvailability\\:\\:getStock\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Event, AppBundle\\\\Event\\\\Model\\\\Event\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$token of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketSpecialPriceRepository\\:\\:findUnusedToken\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/TicketType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Form\\\\VoteType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Form/VoteType.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\+\\=" between \\(float\\|int\\) and mixed results in an error\\.$#',
	'identifier' => 'assignOp.invalid',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'@\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between mixed and "\\\\n" results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between mixed and \' \' results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 4,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between mixed and \' €\' results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 10,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'montant\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 6,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'nom\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'prenom\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'pretty_name\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to float\\.$#',
	'identifier' => 'cast.double',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 4,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'nom\' might not exist on array\\|null\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Offset \'prenom\' might not exist on array\\|null\\.$#',
	'identifier' => 'offsetAccess.notFound',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of method Afup\\\\Site\\\\Utils\\\\Pays\\:\\:obtenirNom\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of method AppBundle\\\\Event\\\\Invoice\\\\EventInvoicePdfGenerator\\:\\:truncate\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/EventInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Invoice\\\\InvoiceService\\:\\:handleInvoicing\\(\\) has parameter \\$address with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Invoice\\\\InvoiceService\\:\\:handleInvoicing\\(\\) has parameter \\$authorization with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Invoice\\\\InvoiceService\\:\\:handleInvoicing\\(\\) has parameter \\$city with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Invoice\\\\InvoiceService\\:\\:handleInvoicing\\(\\) has parameter \\$company with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Invoice\\\\InvoiceService\\:\\:handleInvoicing\\(\\) has parameter \\$countryId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Invoice\\\\InvoiceService\\:\\:handleInvoicing\\(\\) has parameter \\$email with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Invoice\\\\InvoiceService\\:\\:handleInvoicing\\(\\) has parameter \\$eventId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Invoice\\\\InvoiceService\\:\\:handleInvoicing\\(\\) has parameter \\$firstname with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Invoice\\\\InvoiceService\\:\\:handleInvoicing\\(\\) has parameter \\$lastname with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Invoice\\\\InvoiceService\\:\\:handleInvoicing\\(\\) has parameter \\$oldReference with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Invoice\\\\InvoiceService\\:\\:handleInvoicing\\(\\) has parameter \\$paymentInfos with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Invoice\\\\InvoiceService\\:\\:handleInvoicing\\(\\) has parameter \\$paymentType with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Invoice\\\\InvoiceService\\:\\:handleInvoicing\\(\\) has parameter \\$reference with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Invoice\\\\InvoiceService\\:\\:handleInvoicing\\(\\) has parameter \\$status with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Invoice\\\\InvoiceService\\:\\:handleInvoicing\\(\\) has parameter \\$transaction with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Invoice\\\\InvoiceService\\:\\:handleInvoicing\\(\\) has parameter \\$zipcode with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$address of method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setAddress\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$authorization of method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setAuthorization\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$city of method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setCity\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$company of method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setCompany\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$countryId of method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setCountryId\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$email of method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setEmail\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$firstname of method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setFirstname\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$forumId of method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setForumId\\(\\) expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$lastname of method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setLastname\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$paymentInfos of method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setPaymentInfos\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$paymentType of method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setPaymentType\\(\\) expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$reference of method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setReference\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$reference of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getByReference\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$status of method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setStatus\\(\\) expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$transaction of method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setTransaction\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$zipcode of method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setZipcode\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Invoice/InvoiceService.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 13,
	'path' => __DIR__ . '/sources/AppBundle/Event/JsonLd.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPrettyName\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/JsonLd.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTechnicalName\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/JsonLd.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\JsonLd\\:\\:getDataForEvent\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/JsonLd.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Event\\:\\:getDateEndVote\\(\\) should return DateTime but returns DateTime\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Event.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Event\\:\\:setMicTypeEnabled\\(\\) has parameter \\$micTypeEnabled with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Event.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'id\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/GithubUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'login\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/GithubUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\GithubUser\\:\\:__unserialize\\(\\) has parameter \\$serialized with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/GithubUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\GithubUser\\:\\:fromApi\\(\\) has parameter \\$apiData with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/GithubUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\GithubUser\\:\\:getUserIdentifier\\(\\) should return non\\-empty\\-string but returns string\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/GithubUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$avatarUrl of method AppBundle\\\\Event\\\\Model\\\\GithubUser\\:\\:setAvatarUrl\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/GithubUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$company of method AppBundle\\\\Event\\\\Model\\\\GithubUser\\:\\:setCompany\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/GithubUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$githubId of method AppBundle\\\\Event\\\\Model\\\\GithubUser\\:\\:setGithubId\\(\\) expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/GithubUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$login of method AppBundle\\\\Event\\\\Model\\\\GithubUser\\:\\:setLogin\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/GithubUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$name of method AppBundle\\\\Event\\\\Model\\\\GithubUser\\:\\:setName\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/GithubUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$profileUrl of method AppBundle\\\\Event\\\\Model\\\\GithubUser\\:\\:setProfileUrl\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/GithubUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Event\\\\Model\\\\GithubUser\\:\\:\\$id \\(int\\|null\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/GithubUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Event\\\\Model\\\\GithubUser\\:\\:\\$login \\(string\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/GithubUser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:getTickets\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Invoice.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setTickets\\(\\) has parameter \\$tickets with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Invoice.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:\\$tickets \\(array\\<AppBundle\\\\Event\\\\Model\\\\Ticket\\>\\) does not accept array\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Invoice.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\InvoiceFactory\\:\\:createInvoiceFromSponsorTicket\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/InvoiceFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$forumId of method AppBundle\\\\Event\\\\Model\\\\Invoice\\:\\:setForumId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/InvoiceFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Lead\\:\\:jsonSerialize\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Lead.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Meetup\\:\\:getLocation\\(\\) should return string but returns string\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Meetup.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Planning.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Planning\\:\\:setId\\(\\) has parameter \\$id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Planning.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\BadgeRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/BadgeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\BadgeRepository\\:\\:initMetadata\\(\\) return type with generic class CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/BadgeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/BadgeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventCouponRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventCouponRepository\\:\\:changeCouponForEvent\\(\\) has parameter \\$coupons with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventCouponRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventCouponRepository\\:\\:couponsListForEvent\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventCouponRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventCouponRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventCouponRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventCouponRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventCouponRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(mixed\\)\\: mixed\\)\\|null, Closure\\(AppBundle\\\\Event\\\\Model\\\\EventCoupon\\)\\: string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventCouponRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventCouponRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$iterator of function iterator_to_array expects iterable, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventCouponRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface\\<AppBundle\\\\Event\\\\Model\\\\Event\\>\\|null supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'est_supprimable\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'id\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'id_forum\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'quantity\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:countRelation\\(\\) should return array\\<int, int\\> but returns array\\<mixed\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getAllActive\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getAllPastEventWithSpeakerEmail\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getAllPastEventWithSpeakerEmail\\(\\) has parameter \\$email with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getAllPastEventWithTegistrationEmail\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getAllPastEventWithTegistrationEmail\\(\\) has parameter \\$email with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getAllSortedByTitre\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getCurrentEvent\\(\\) should return AppBundle\\\\Event\\\\Model\\\\Event\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getLastEvent\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getLastYearEvent\\(\\) should return AppBundle\\\\Event\\\\Model\\\\Event but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getList\\(\\) has parameter \\$id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getList\\(\\) return type has no value type specified in iterable type list\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getMostRecentEvent\\(\\) should return AppBundle\\\\Event\\\\Model\\\\Event\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getNextEventForGithubUser\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getPreviousEvents\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getPreviousEventsBefore\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:getPreviousForum\\(\\) should return int\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\<string\\>\\|string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'talks\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventStatsRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDateEndSales\\(\\) on AppBundle\\\\Event\\\\Model\\\\Event\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventStatsRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventStatsRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot use \\+\\+ on mixed\\.$#',
	'identifier' => 'postInc.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventStatsRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\EventStatsRepository\\:\\:getRegistrationTracking\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventStatsRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$confirmed of class AppBundle\\\\Event\\\\Model\\\\EventStats\\\\TicketTypeStats constructor expects list\\<int\\>, array\\<mixed\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventStatsRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$registered of class AppBundle\\\\Event\\\\Model\\\\EventStats\\\\DailyStats constructor expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventStatsRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$talks of class AppBundle\\\\Event\\\\Model\\\\EventStats\\\\CFPStats constructor expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventStatsRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$confirmed of class AppBundle\\\\Event\\\\Model\\\\EventStats\\\\DailyStats constructor expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventStatsRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$registered of class AppBundle\\\\Event\\\\Model\\\\EventStats\\\\TicketTypeStats constructor expects list\\<int\\>, array\\<mixed\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventStatsRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$speakers of class AppBundle\\\\Event\\\\Model\\\\EventStats\\\\CFPStats constructor expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventStatsRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$paying of class AppBundle\\\\Event\\\\Model\\\\EventStats\\\\TicketTypeStats constructor expects list\\<int\\>, array\\<mixed\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventStatsRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$pending of class AppBundle\\\\Event\\\\Model\\\\EventStats\\\\DailyStats constructor expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventStatsRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#4 \\$paid of class AppBundle\\\\Event\\\\Model\\\\EventStats\\\\DailyStats constructor expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventStatsRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 4,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/EventStatsRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Event\\\\Model\\\\Repository\\\\GithubUserRepository implements generic interface Symfony\\\\Component\\\\Security\\\\Core\\\\User\\\\UserProviderInterface but does not specify its types\\: TUser$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/GithubUserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\GithubUserRepository\\:\\:getAllOrderedByLogin\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/GithubUserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\GithubUserRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/GithubUserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\GithubUserRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/GithubUserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/GithubUserRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPrice\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketEventType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/InvoiceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\InvoiceRepository\\:\\:getByEventId\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/InvoiceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\InvoiceRepository\\:\\:getPendingBankwires\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/InvoiceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\InvoiceRepository\\:\\:getWithEventDataByReference\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/InvoiceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\InvoiceRepository\\:\\:getWithEventDataByReference\\(\\) should return array\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/InvoiceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\InvoiceRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/InvoiceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\InvoiceRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/InvoiceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\InvoiceRepository\\:\\:searchAllPastEventsInvoices\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/InvoiceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\InvoiceRepository\\:\\:searchAllQuotesAndInvoices\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/InvoiceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/InvoiceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function implode expects array\\<string\\>, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/InvoiceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\MeetupRepository\\:\\:findAllForAntenne\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/MeetupRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\MeetupRepository\\:\\:findNextForAntenne\\(\\) should return AppBundle\\\\Event\\\\Model\\\\Meetup\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/MeetupRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\MeetupRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/MeetupRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\MeetupRepository\\:\\:initMetadata\\(\\) return type with generic class CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/MeetupRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/MeetupRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\PlanningRepository\\:\\:getByTalk\\(\\) should return AppBundle\\\\Event\\\\Model\\\\Planning\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/PlanningRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\PlanningRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/PlanningRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\PlanningRepository\\:\\:initMetadata\\(\\) return type with generic class CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/PlanningRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/PlanningRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\RoomRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/RoomRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\RoomRepository\\:\\:initMetadata\\(\\) return type with generic class CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/RoomRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/RoomRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset 0 on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$cfp on mixed\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$nb on mixed\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SpeakerRepository\\:\\:getFromLastEventAndUserId\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SpeakerRepository\\:\\:getFromLastEventAndUserId\\(\\) has parameter \\$eventId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SpeakerRepository\\:\\:getFromLastEventAndUserId\\(\\) has parameter \\$githubUserId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SpeakerRepository\\:\\:getScheduledSpeakersByEvent\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SpeakerRepository\\:\\:getSpeakersByEvent\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SpeakerRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SpeakerRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SpeakerRepository\\:\\:searchSpeakers\\(\\) has parameter \\$direction with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SpeakerRepository\\:\\:searchSpeakers\\(\\) has parameter \\$sort with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SpeakerRepository\\:\\:searchSpeakers\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$key of static method Webmozart\\\\Assert\\\\Assert\\:\\:keyExists\\(\\) expects int\\|string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SpeakerSuggestionRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerSuggestionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SpeakerSuggestionRepository\\:\\:initMetadata\\(\\) return type with generic class CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerSuggestionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SpeakerSuggestionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SponsorScanRepository\\:\\:getBySponsorTicket\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SponsorScanRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SponsorScanRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SponsorScanRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SponsorScanRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SponsorScanRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SponsorScanRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SponsorTicketRepository\\:\\:getByEvent\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SponsorTicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SponsorTicketRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SponsorTicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SponsorTicketRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SponsorTicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/SponsorTicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkInvitationRepository\\:\\:getPendingInvitationsByTalkId\\(\\) has parameter \\$talkId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkInvitationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkInvitationRepository\\:\\:getPendingInvitationsByTalkId\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkInvitationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkInvitationRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkInvitationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkInvitationRepository\\:\\:initMetadata\\(\\) return type with generic class CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkInvitationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkInvitationRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'\\.aggregation\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'planning\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'room\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'speaker\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'talk\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset 0 on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$vote_note on mixed\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$vote_total on mixed\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:getAllByEventWithSpeakers\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:getAllPastTalks\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:getAllTalksAndRatingsForUser\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:getByTalkWithSpeakers\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:getNewTalksToRate\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:getNumberOfTalksByEvent\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:getNumberOfTalksByEventAndLanguage\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:getNumberOfTalksByEventAndLanguage\\(\\) has parameter \\$languageCode with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:getPreviousTalksBySpeaker\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:getTalkOfTheDay\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:getTalksBySpeaker\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:getTalksBySpeakerWithVotes\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:initMetadata\\(\\) return type with generic class CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$note of class AppBundle\\\\Event\\\\Model\\\\TalkAggregateVote constructor expects float, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$talk of class AppBundle\\\\Event\\\\Model\\\\TalkAggregate constructor expects AppBundle\\\\Event\\\\Model\\\\Talk, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$speakers of class AppBundle\\\\Event\\\\Model\\\\TalkAggregate constructor expects array\\<AppBundle\\\\Event\\\\Model\\\\Speaker\\>, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$total of class AppBundle\\\\Event\\\\Model\\\\TalkAggregateVote constructor expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$room of class AppBundle\\\\Event\\\\Model\\\\TalkAggregate constructor expects AppBundle\\\\Event\\\\Model\\\\Room\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#4 \\$planning of class AppBundle\\\\Event\\\\Model\\\\TalkAggregate constructor expects AppBundle\\\\Event\\\\Model\\\\Planning\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'count\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkToSpeakersRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkToSpeakersRepository\\:\\:getNumberOfSpeakers\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkToSpeakersRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkToSpeakersRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkToSpeakersRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkToSpeakersRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkToSpeakersRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TalkToSpeakersRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketEventTypeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getIsRestrictedToMembers\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketEventTypeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketEventTypeRepository\\:\\:doesEventHasRestrictedToMembersTickets\\(\\) has parameter \\$datesFilter with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketEventTypeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketEventTypeRepository\\:\\:getTicketsByEvent\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketEventTypeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketEventTypeRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketEventTypeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketEventTypeRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketEventTypeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketEventTypeRepository\\:\\:update\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketEventTypeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketEventTypeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$datesFilter of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketEventTypeRepository\\:\\:getTicketsByEvent\\(\\) expects int\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketEventTypeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'@\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'aff\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'aft\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'afto\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'sold_tickets\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'ticket\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'total\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset 0 on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$last_subscription on mixed\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getAllTicketsForExport\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getByInvoiceWithDetail\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getPublicSoldTickets\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getPublicSoldTicketsByDay\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getPublicSoldTicketsByDay\\(\\) has parameter \\$day with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getPublicSoldTicketsByDayOfType\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getPublicSoldTicketsByDayOfType\\(\\) has parameter \\$day with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getPublicSoldTicketsOfType\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getRegistrationsForEventsWithNewsletterAllowed\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getTotalOfSoldTicketsByMember\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getTotalOfSoldTicketsByMember\\(\\) has parameter \\$eventId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getTotalOfSoldTicketsByMember\\(\\) has parameter \\$userId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getTotalOfSoldTicketsByMember\\(\\) has parameter \\$userType with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getWithTicketTypeByReference\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:searchAllPastEvents\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$ticket of class AppBundle\\\\Event\\\\Model\\\\TicketAggregate constructor expects AppBundle\\\\Event\\\\Model\\\\Ticket, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$ticketType of class AppBundle\\\\Event\\\\Model\\\\TicketAggregate constructor expects AppBundle\\\\Event\\\\Model\\\\TicketType, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$invoice of class AppBundle\\\\Event\\\\Model\\\\TicketAggregate constructor expects AppBundle\\\\Event\\\\Model\\\\Invoice\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketSpecialPriceRepository\\:\\:findUnusedToken\\(\\) should return AppBundle\\\\Event\\\\Model\\\\TicketSpecialPrice\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketSpecialPriceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketSpecialPriceRepository\\:\\:getByEvent\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketSpecialPriceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketSpecialPriceRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketSpecialPriceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketSpecialPriceRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketSpecialPriceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketSpecialPriceRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketTypeRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketTypeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketTypeRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketTypeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/TicketTypeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\UserBadgeRepository\\:\\:findByUserId\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/UserBadgeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\UserBadgeRepository\\:\\:findByUserId\\(\\) has parameter \\$userId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/UserBadgeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\UserBadgeRepository\\:\\:getHydratorForUserBadge\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/UserBadgeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\UserBadgeRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/UserBadgeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\UserBadgeRepository\\:\\:initMetadata\\(\\) return type with generic class CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/UserBadgeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/UserBadgeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$hydrator of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Event\\\\Model\\\\UserBadge\\>\\:\\:getCollection\\(\\) expects CCMBenchmark\\\\Ting\\\\Repository\\\\HydratorInterface\\<mixed\\>\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/UserBadgeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type U in call to method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Event\\\\Model\\\\UserBadge\\>\\:\\:getCollection\\(\\)$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/UserBadgeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/VoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\VoteRepository\\:\\:getNumberOfVotesByEvent\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/VoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\VoteRepository\\:\\:getVotesByEvent\\(\\) return type with generic interface CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/VoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\VoteRepository\\:\\:getVotesByTalkWithUser\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/VoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\VoteRepository\\:\\:getVotesByTalkWithUser\\(\\) has parameter \\$talkId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/VoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\VoteRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/VoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Repository\\\\VoteRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/VoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/VoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$submittedOn of method AppBundle\\\\Event\\\\Model\\\\Vote\\:\\:setSubmittedOn\\(\\) expects DateTime, DateTime\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Repository/VoteRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Event\\\\Model\\\\Room\\:\\:\\$name \\(string\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Room.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Speaker\\:\\:getHotelNightsArray\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Speaker.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Speaker\\:\\:setHotelNightsArray\\(\\) has parameter \\$hotelNights with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Speaker.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Speaker\\:\\:setLinkedin\\(\\) has parameter \\$linkedin with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Speaker.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Speaker\\:\\:validate\\(\\) has parameter \\$payload with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Speaker.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function implode expects array\\<string\\>, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Speaker.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Event\\\\Model\\\\Speaker\\:\\:\\$linkedin \\(string\\|null\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Speaker.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Talk\\:\\:getVotes\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Talk.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Talk\\:\\:setVotes\\(\\) has parameter \\$votes with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Talk.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type string\\|null\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Talk.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Event\\\\Model\\\\Talk\\:\\:\\$abstract \\(string\\) does not accept string\\|null\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Talk.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Event\\\\Model\\\\Talk\\:\\:\\$votes \\(array\\<AppBundle\\\\Event\\\\Model\\\\Vote\\>\\) does not accept array\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Talk.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Ticket\\:\\:getTags\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Ticket.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/TicketType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\TicketType\\:\\:getDays\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/TicketType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Tweet.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Tweet.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Tweet\\:\\:setId\\(\\) has parameter \\$id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Tweet.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Tweet\\:\\:setTalkId\\(\\) has parameter \\$talkId with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Tweet.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Model\\\\Vote\\:\\:getId\\(\\) should return int but returns int\\|null\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Model/Vote.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'\\.aggregation\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'speaker\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'talk\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Speaker\\\\ExportGenerator\\:\\:prepareLine\\(\\) has parameter \\$talks with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Speaker\\\\ExportGenerator\\:\\:prepareLine\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$speaker of method AppBundle\\\\Event\\\\Speaker\\\\ExportGenerator\\:\\:prepareLine\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Speaker, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'choices\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'files\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'has_special_diet\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'nights\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'phone_number\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'special_diet_description\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'type\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'will_attend\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$file of method AppBundle\\\\SpeakerInfos\\\\SpeakersExpensesStorage\\:\\:store\\(\\) expects Symfony\\\\Component\\\\HttpFoundation\\\\File\\\\UploadedFile, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$micType of method AppBundle\\\\Event\\\\Model\\\\Speaker\\:\\:setMicType\\(\\) expects AppBundle\\\\Event\\\\Speaker\\\\MicrophoneType\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$object of static method DateTimeImmutable\\:\\:createFromMutable\\(\\) expects DateTime, DateTime\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$phoneNumber of method AppBundle\\\\Event\\\\Model\\\\Speaker\\:\\:setPhoneNumber\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$route of method Symfony\\\\Bundle\\\\FrameworkBundle\\\\Controller\\\\AbstractController\\:\\:redirectToRoute\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$specialDietDescription of method AppBundle\\\\Event\\\\Model\\\\Speaker\\:\\:setSpecialDietDescription\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\.\\.\\.\\$arrays of function array_merge expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type int\\|null\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Event/Speaker/SpeakerPage.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPath\\(\\) on AppBundle\\\\Event\\\\Model\\\\Event\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Sponsorship/SponsorshipLeadMail.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTitle\\(\\) on AppBundle\\\\Event\\\\Model\\\\Event\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Sponsorship/SponsorshipLeadMail.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'\\.aggregation\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'speaker\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'talk\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getLabel\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Talk\\\\ExportGenerator\\:\\:getSpeakersLabels\\(\\) has parameter \\$speakers with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Talk\\\\ExportGenerator\\:\\:getSpeakersLabels\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Talk\\\\ExportGenerator\\:\\:getSpeakersLocalities\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Talk\\\\ExportGenerator\\:\\:prepareLine\\(\\) has parameter \\$speakers with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Talk\\\\ExportGenerator\\:\\:prepareLine\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Talk\\\\ExportGenerator\\:\\:prepareSpeakersLabel\\(\\) has parameter \\$speakers with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$speakers of method AppBundle\\\\Event\\\\Talk\\\\ExportGenerator\\:\\:prepareSpeakersLocalities\\(\\) expects array\\<AppBundle\\\\Event\\\\Model\\\\Speaker\\>, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$talk of method AppBundle\\\\Event\\\\Talk\\\\ExportGenerator\\:\\:prepareLine\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Talk, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function implode expects array\\<string\\>, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$speakers of method AppBundle\\\\Event\\\\Talk\\\\ExportGenerator\\:\\:prepareLine\\(\\) expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/ExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Talk\\\\InvitationFormHandler\\:\\:handle\\(\\) has parameter \\$form with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/InvitationFormHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Talk\\\\TalkFormHandler\\:\\:handle\\(\\) has parameter \\$form with generic interface Symfony\\\\Component\\\\Form\\\\FormInterface but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Talk/TalkFormHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getCompanyId\\(\\) on AppBundle\\\\Association\\\\Model\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/PurchaseTypeFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method hasRole\\(\\) on AppBundle\\\\Association\\\\Model\\\\User\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/PurchaseTypeFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Ticket\\\\PurchaseTypeFactory\\:\\:getPurchaseForUser\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/PurchaseTypeFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Ticket\\\\PurchaseTypeFactory\\:\\:getPurchaseForUser\\(\\) has parameter \\$specialPriceToken with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/PurchaseTypeFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$specialPriceToken of method AppBundle\\\\Event\\\\Model\\\\Ticket\\:\\:setSpecialPriceToken\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/PurchaseTypeFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_key_exists expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/RegistrationsExportGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getReference\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/SponsorTicketHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Ticket\\\\SponsorTicketHelper\\:\\:getRegisteredTickets\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/SponsorTicketHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\Event\\\\Model\\\\Invoice\\>\\:\\:save\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Invoice, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/SponsorTicketHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$reference of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TicketRepository\\:\\:getByReference\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/SponsorTicketHelper.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Ticket\\\\SponsorTokenMail\\:\\:sendNotification\\(\\) has parameter \\$lastCall with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/SponsorTokenMail.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPrettyName\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/TicketOffers.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#4 \\$availableTickets of class AppBundle\\\\Event\\\\Model\\\\TicketOffer constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/TicketOffers.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\-" between int and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/TicketTypeAvailability.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\-" between int\\|null and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/TicketTypeAvailability.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDay\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/TicketTypeAvailability.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDays\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/TicketTypeAvailability.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTechnicalName\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/TicketTypeAvailability.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of function count expects array\\|Countable, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Ticket/TicketTypeAvailability.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Validator\\\\Constraints\\\\AvailableTicketValidator\\:\\:validate\\(\\) has parameter \\$ticket with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Validator/Constraints/AvailableTicketValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$event of method AppBundle\\\\Event\\\\Ticket\\\\TicketTypeAvailability\\:\\:getStock\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Event, AppBundle\\\\Event\\\\Model\\\\Event\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Validator/Constraints/AvailableTicketValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Validator/Constraints/CorporateMemberValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\+" between mixed and int\\<1, max\\> results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Validator/Constraints/CorporateMemberValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getIsRestrictedToMembers\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Validator/Constraints/CorporateMemberValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Validator\\\\Constraints\\\\CorporateMemberValidator\\:\\:validate\\(\\) has parameter \\$tickets with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Validator/Constraints/CorporateMemberValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getIsRestrictedToMembers\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Validator/Constraints/LoggedInMemberValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Validator\\\\Constraints\\\\LoggedInMemberValidator\\:\\:validate\\(\\) has parameter \\$ticket with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Validator/Constraints/LoggedInMemberValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getIsRestrictedToMembers\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Validator/Constraints/PublicTicketValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Event\\\\Validator\\\\Constraints\\\\PublicTicketValidator\\:\\:validate\\(\\) has parameter \\$ticket with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Event/Validator/Constraints/PublicTicketValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Form\\\\BooleanType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Form/BooleanType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\Attendee\\:\\:getConsultationDate\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/Attendee.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\Attendee\\:\\:getEmail\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/Attendee.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\Attendee\\:\\:getFirstname\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/Attendee.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\Attendee\\:\\:getId\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/Attendee.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\Attendee\\:\\:getLastname\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/Attendee.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\Attendee\\:\\:getLogin\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/Attendee.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\Attendee\\:\\:getNearestOffice\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/Attendee.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\Attendee\\:\\:getPowerFirstname\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/Attendee.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\Attendee\\:\\:getPowerId\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/Attendee.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\Attendee\\:\\:getPowerLastname\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/Attendee.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\Attendee\\:\\:getPresence\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/Attendee.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\GeneralMeeting\\:\\:getId\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeeting.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\GeneralMeeting\\:\\:getPersonneAvecPouvoirId\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeeting.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\GeneralMeeting\\:\\:getPersonnePhysiqueId\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeeting.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\GeneralMeeting\\:\\:getPresence\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeeting.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\GeneralMeeting\\\\GeneralMeetingQuestionFormType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingQuestionFormType.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'@\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 9,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between mixed and \' \' results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 13,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:findOneByDate\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:getAttendees\\(\\) has parameter \\$direction with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:getPowerSelectionList\\(\\) should return array\\<int, string\\> but returns array\\<string\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:getValidAttendeeIds\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\GeneralMeeting\\\\GeneralMeetingRepository\\:\\:obtenirDescription\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#10 \\$powerLastname of class AppBundle\\\\GeneralMeeting\\\\Attendee constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#11 \\$powerFirstname of class AppBundle\\\\GeneralMeeting\\\\Attendee constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$email of class AppBundle\\\\GeneralMeeting\\\\Attendee constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$order of method Doctrine\\\\DBAL\\\\Query\\\\QueryBuilder\\:\\:orderBy\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$login of class AppBundle\\\\GeneralMeeting\\\\Attendee constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#4 \\$lastname of class AppBundle\\\\GeneralMeeting\\\\Attendee constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#5 \\$firstname of class AppBundle\\\\GeneralMeeting\\\\Attendee constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#6 \\$nearestOffice of class AppBundle\\\\GeneralMeeting\\\\Attendee constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/GeneralMeetingRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\GeneralMeeting\\\\PrepareFormType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/GeneralMeeting/PrepareFormType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Github\\\\Exception\\\\UnableToFindGithubUserException\\:\\:__construct\\(\\) has parameter \\$username with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Github/Exception/UnableToFindGithubUserException.php',
];
$ignoreErrors[] = [
	'message' => '#^Part \\$username \\(mixed\\) of encapsed string cannot be cast to string\\.$#',
	'identifier' => 'encapsedStringPart.nonString',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Github/Exception/UnableToFindGithubUserException.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Github\\\\Exception\\\\UnableToGetGithubUserInfosException\\:\\:__construct\\(\\) has parameter \\$payload with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Github/Exception/UnableToGetGithubUserInfosException.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Github\\\\Exception\\\\UnableToGetGithubUserInfosException\\:\\:__construct\\(\\) has parameter \\$status with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Github/Exception/UnableToGetGithubUserInfosException.php',
];
$ignoreErrors[] = [
	'message' => '#^Part \\$payload \\(mixed\\) of encapsed string cannot be cast to string\\.$#',
	'identifier' => 'encapsedStringPart.nonString',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Github/Exception/UnableToGetGithubUserInfosException.php',
];
$ignoreErrors[] = [
	'message' => '#^Part \\$status \\(mixed\\) of encapsed string cannot be cast to string\\.$#',
	'identifier' => 'encapsedStringPart.nonString',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Github/Exception/UnableToGetGithubUserInfosException.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setSettings\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Indexation/Meetups/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Indexation\\\\Meetups\\\\Runner\\:\\:getTransformedMeetupsFromDatabase\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Indexation/Meetups/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Indexation\\\\Meetups\\\\Runner\\:\\:initIndex\\(\\) should return Algolia\\\\AlgoliaSearch\\\\SearchIndex but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Indexation/Meetups/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$urlName on AppBundle\\\\Antennes\\\\Meetup\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Indexation/Meetups/Transformer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Indexation\\\\Meetups\\\\Transformer\\:\\:transform\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Indexation/Meetups/Transformer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method setSettings\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Indexation/Talks/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Indexation\\\\Talks\\\\Runner\\:\\:initIndex\\(\\) should return Algolia\\\\AlgoliaSearch\\\\SearchIndex but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Indexation/Talks/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Indexation\\\\Talks\\\\Runner\\:\\:prepareObject\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Indexation/Talks/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Indexation\\\\Talks\\\\Transformer\\:\\:transform\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Indexation/Talks/Transformer.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinComments.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'comment\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinComments.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'created_date\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinComments.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'rating\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinComments.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'user_display_name\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinComments.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinComments.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Joindin\\\\JoindinComments\\:\\:callJoindInApi\\(\\) should return string but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinComments.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Joindin\\\\JoindinComments\\:\\:getCommentsFromTalk\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinComments.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Joindin\\\\JoindinComments\\:\\:getCommmentsFromId\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinComments.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$joindinId of method AppBundle\\\\Joindin\\\\JoindinComments\\:\\:getCommmentsFromId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinComments.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'stub\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinTalk.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset 0 on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinTalk.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Joindin\\\\JoindinTalk\\:\\:callJoindInApi\\(\\) should return string but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinTalk.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Joindin\\\\JoindinTalk\\:\\:getStubFromTalk\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinTalk.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Joindin\\\\JoindinTalk\\:\\:prepareStubFromJoindinResponse\\(\\) should return string\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinTalk.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Joindin\\\\JoindinTalk\\:\\:readResponse\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinTalk.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$joindinId of method AppBundle\\\\Joindin\\\\JoindinTalk\\:\\:callJoindInApi\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Joindin/JoindinTalk.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$locale of method Symfony\\\\Component\\\\HttpFoundation\\\\Request\\:\\:setLocale\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Listener/LocaleEventSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\EventEventSubscriber\\:\\:__construct\\(\\) has parameter \\$membersList with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/EventEventSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$list of method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:unSubscribeAddress\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/EventEventSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "/" between mixed and 50 results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'email_address\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'members\' on array\\|false\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'total_items\' on array\\|false\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:archiveAddress\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:callMembersAddresses\\(\\) should return array\\<string\\> but returns array\\<int\\<0, max\\>, mixed\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:createCampaign\\(\\) has parameter \\$settings with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:createCampaign\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:createCampaign\\(\\) should return array but returns array\\|false\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:createTemplate\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:getAllCleanedMembersAddresses\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:getAllSubscribedMembersAddresses\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:getAllUnSubscribedMembersAddresses\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:scheduleCampaign\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:subscribeAddress\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:subscribeAddressWithoutConfirmation\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:unSubscribeAddress\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_unique expects an array of values castable to string, list given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Mailchimp.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between mixed and \' \\: \' results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/MailchimpMembersAutoListSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEmail\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/MailchimpMembersAutoListSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\MailchimpMembersAutoListSynchronizer\\:\\:archiveAddresses\\(\\) has parameter \\$emails with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/MailchimpMembersAutoListSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\MailchimpMembersAutoListSynchronizer\\:\\:getSubscribedEmailsOnWebsite\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/MailchimpMembersAutoListSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\MailchimpMembersAutoListSynchronizer\\:\\:subscribeAddresses\\(\\) has parameter \\$emails with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/MailchimpMembersAutoListSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(mixed\\)\\: mixed\\)\\|null, Closure\\(string\\)\\: lowercase\\-string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 4,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/MailchimpMembersAutoListSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$email of method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:archiveAddress\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/MailchimpMembersAutoListSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$email of method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:subscribeAddressWithoutConfirmation\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/MailchimpMembersAutoListSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/MailchimpMembersAutoListSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEmail\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\Runner\\:\\:initList\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Mailchimp\\\\Runner\\:\\:updateList\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$email of method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:subscribeAddress\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/Runner.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Mailchimp\\\\SubscriberType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Mailchimp/SubscriberType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\MembershipFee\\\\Form\\\\MembershipFeeType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/Form/MembershipFeeType.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\-" between mixed and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on DateTimeImmutable\\|false\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getAmount\\(\\) on AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getClientReference\\(\\) on AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getEndDate\\(\\) on AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getInvoiceDate\\(\\) on AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getInvoiceNumber\\(\\) on AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 4,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getStartDate\\(\\) on AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTimestamp\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getUserId\\(\\) on AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getUserType\\(\\) on AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\MembershipFee\\\\MembershipFeeInvoicePdfGenerator\\:\\:buildDetailsPersonneMorale\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\MembershipFee\\\\MembershipFeeInvoicePdfGenerator\\:\\:buildDetailsPersonnePhysique\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$applicationDate of method AppBundle\\\\Compta\\\\BankAccount\\\\BankAccountFactory\\:\\:createApplyableAt\\(\\) expects DateTimeInterface\\|null, DateTimeImmutable\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$date of static method Afup\\\\Site\\\\Utils\\\\Vat\\:\\:isSubjectedToVat\\(\\) expects DateTimeInterface, DateTimeImmutable\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$num of function number_format expects float, float\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of method AppBundle\\\\MembershipFee\\\\MembershipFeeInvoicePdfGenerator\\:\\:formatFactureValue\\(\\) expects float, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$montant of method AppBundle\\\\MembershipFee\\\\MembershipFeeInvoicePdfGenerator\\:\\:buildDetailsPersonneMorale\\(\\) expects float, float\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$montant of method AppBundle\\\\MembershipFee\\\\MembershipFeeInvoicePdfGenerator\\:\\:buildDetailsPersonnePhysique\\(\\) expects float, float\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeInvoicePdfGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$value on AppBundle\\\\Association\\\\MemberType\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeMailer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getStartDate\\(\\) on AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeMailer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTimestamp\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeMailer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getUserId\\(\\) on AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeMailer.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getUserType\\(\\) on AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeMailer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$entity of method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\>\\:\\:delete\\(\\) expects AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee, AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/MembershipFeeService.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'@\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/Model/Repository/MembershipFeeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/Model/Repository/MembershipFeeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'date_fin\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/Model/Repository/MembershipFeeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset 0 on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/Model/Repository/MembershipFeeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$number on mixed\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/Model/Repository/MembershipFeeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\MembershipFee\\\\Model\\\\Repository\\\\MembershipFeeRepository\\:\\:getLatestByUserTypeAndId\\(\\) should return AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/Model/Repository/MembershipFeeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\MembershipFee\\\\Model\\\\Repository\\\\MembershipFeeRepository\\:\\:getListByUserTypeAndId\\(\\) should return CCMBenchmark\\\\Ting\\\\Repository\\\\Collection\\<AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\> but returns CCMBenchmark\\\\Ting\\\\Repository\\\\CollectionInterface\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/Model/Repository/MembershipFeeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\MembershipFee\\\\Model\\\\Repository\\\\MembershipFeeRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/Model/Repository/MembershipFeeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\MembershipFee\\\\Model\\\\Repository\\\\MembershipFeeRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/Model/Repository/MembershipFeeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\MembershipFee\\\\Model\\\\Repository\\\\MembershipFeeRepository\\:\\:updatePayment\\(\\) should return bool but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/Model/Repository/MembershipFeeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/Model/Repository/MembershipFeeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type U in call to method CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\>\\:\\:getCollection\\(\\)$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/Model/Repository/MembershipFeeRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on AppBundle\\\\MembershipFee\\\\Model\\\\MembershipFee\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/OnlinePaymentHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTimestamp\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/OnlinePaymentHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of method AppBundle\\\\MembershipFee\\\\MembershipFeeService\\:\\:updatePayment\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/OnlinePaymentHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#6 \\$dateDebut of method AppBundle\\\\MembershipFee\\\\MembershipFeeService\\:\\:ajouter\\(\\) expects int, int\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/MembershipFee/OnlinePaymentHandler.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access constant class on T of mixed\\.$#',
	'identifier' => 'classConstant.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Model/CollectionFilter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Model\\\\CollectionFilter\\:\\:filter\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Model/CollectionFilter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$object_or_class of function method_exists expects object\\|string, T given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Model/CollectionFilter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Model\\\\ComptaModeReglement\\:\\:list\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Model/ComptaModeReglement.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Offices\\\\OfficeFinder\\:\\:findInfos\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Offices/OfficeFinder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Offices\\\\OfficeFinder\\:\\:findOffice\\(\\) should return string\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Offices/OfficeFinder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Offices\\\\OfficeFinder\\:\\:geocode\\(\\) should return Geocoder\\\\Model\\\\AddressCollection\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Offices/OfficeFinder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Offices\\\\OfficeFinder\\:\\:geocodeAddresses\\(\\) has parameter \\$addresses with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Offices/OfficeFinder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Offices\\\\OfficeFinder\\:\\:locateNearestLocalOffice\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Offices/OfficeFinder.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$address of method AppBundle\\\\Offices\\\\OfficeFinder\\:\\:geocode\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Offices/OfficeFinder.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type string\\|null\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Offices/OfficeFinder.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Offices\\\\OfficeFinder\\:\\:\\$geocodeCache type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Offices/OfficeFinder.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Openfeedback\\\\OpenfeedbackJsonGenerator\\:\\:generate\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Openfeedback/OpenfeedbackJsonGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$date of method AppBundle\\\\Openfeedback\\\\OpenfeedbackJsonGenerator\\:\\:getOpenfeedbackFormat\\(\\) expects DateTime, DateTime\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Openfeedback/OpenfeedbackJsonGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type int\\|null\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Openfeedback/OpenfeedbackJsonGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'  \\<input type\\=…\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'\\<form method\\="POST"…\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between \'PBX_ANNULE\\=\'\\|\'PBX_BILLING\\=\'\\|\'PBX_CMD\\=\'\\|\'PBX_DEVISE\\=\'\\|\'PBX_EFFECTUE\\=\'\\|\'PBX_HASH\\=\'\\|\'PBX_IDENTIFIANT\\=\'\\|\'PBX_LANGUE\\=\'\\|\'PBX_PORTEUR\\=\'\\|\'PBX_RANG\\=\'\\|\'PBX_REFUSE\\=\'\\|\'PBX_REPONDRE_A\\=\'\\|\'PBX_RETOUR\\=\'\\|\'PBX_SHOPPINGCART\\=\'\\|\'PBX_SITE\\=\'\\|\'PBX_SOURCE\\=\'\\|\'PBX_TIME\\=\'\\|\'PBX_TOTAL\\=\'\\|\'PBX_TYPECARTE\\=\'\\|\'PBX_TYPEPAIEMENT\\=\' and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\Paybox\\:\\:__construct\\(\\) has parameter \\$domainServer with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\Paybox\\:\\:__construct\\(\\) has parameter \\$identifiant with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\Paybox\\:\\:__construct\\(\\) has parameter \\$rang with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\Paybox\\:\\:__construct\\(\\) has parameter \\$secretKey with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\Paybox\\:\\:__construct\\(\\) has parameter \\$site with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\Paybox\\:\\:generate\\(\\) has parameter \\$quantity with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\Paybox\\:\\:generatePbxShoppingcart\\(\\) has parameter \\$quantity with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\Paybox\\:\\:preparePbxBillingValue\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\Paybox\\:\\:preparePbxBillingValue\\(\\) has parameter \\$default with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\Paybox\\:\\:preparePbxBillingValue\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function str_replace expects array\\<string\\>\\|string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Payment\\\\Paybox\\:\\:\\$cmd has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Payment\\\\Paybox\\:\\:\\$porteur has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Payment\\\\Paybox\\:\\:\\$total has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Payment\\\\Paybox\\:\\:\\$urlRepondreA has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Payment\\\\Paybox\\:\\:\\$urlRetourAnnule has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Payment\\\\Paybox\\:\\:\\$urlRetourEffectue has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Payment\\\\Paybox\\:\\:\\$urlRetourRefuse has no type specified\\.$#',
	'identifier' => 'missingType.property',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Property DOMNode\\:\\:\\$nodeValue \\(string\\|null\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 6,
	'path' => __DIR__ . '/sources/AppBundle/Payment/Paybox.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\PayboxBilling\\:\\:__construct\\(\\) has parameter \\$address1 with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxBilling.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\PayboxBilling\\:\\:__construct\\(\\) has parameter \\$city with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxBilling.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\PayboxBilling\\:\\:__construct\\(\\) has parameter \\$countryCode with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxBilling.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\PayboxBilling\\:\\:__construct\\(\\) has parameter \\$firstName with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxBilling.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\PayboxBilling\\:\\:__construct\\(\\) has parameter \\$lastName with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxBilling.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\PayboxBilling\\:\\:__construct\\(\\) has parameter \\$zipCode with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxBilling.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$alpha2 of method League\\\\ISO3166\\\\ISO3166\\:\\:alpha2\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxBilling.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\*" between mixed and 100 results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\PayboxFactory\\:\\:__construct\\(\\) has parameter \\$payboxDomainServer with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\PayboxFactory\\:\\:__construct\\(\\) has parameter \\$payboxIdentifiant with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\PayboxFactory\\:\\:__construct\\(\\) has parameter \\$payboxRang with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\PayboxFactory\\:\\:__construct\\(\\) has parameter \\$payboxSecretKey with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\PayboxFactory\\:\\:__construct\\(\\) has parameter \\$payboxSite with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Payment\\\\PayboxFactory\\:\\:createPayboxForTicket\\(\\) has parameter \\$amount with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$cmd of class AppBundle\\\\Payment\\\\PayboxResponse constructor expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxResponseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$status of class AppBundle\\\\Payment\\\\PayboxResponse constructor expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxResponseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#4 \\$authorizationId of class AppBundle\\\\Payment\\\\PayboxResponse constructor expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxResponseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#5 \\$transactionId of class AppBundle\\\\Payment\\\\PayboxResponse constructor expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Payment/PayboxResponseFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Planete\\\\FeedFormType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Planete/FeedFormType.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Routing\\\\LegacyRouter\\:\\:getAdminUrl\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Routing/LegacyRouter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Routing\\\\LegacyRouter\\:\\:getAdminUrl\\(\\) has parameter \\$page with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Routing/LegacyRouter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Security\\\\ActionThrottling\\\\ActionThrottling\\:\\:clearLogsForIp\\(\\) has parameter \\$action with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/ActionThrottling/ActionThrottling.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Security\\\\ActionThrottling\\\\ActionThrottling\\:\\:clearLogsForIp\\(\\) has parameter \\$ip with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/ActionThrottling/ActionThrottling.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$action of method AppBundle\\\\Security\\\\ActionThrottling\\\\LogRepository\\:\\:removeLogs\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/ActionThrottling/ActionThrottling.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$ip of method AppBundle\\\\Security\\\\ActionThrottling\\\\Log\\:\\:setIp\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/ActionThrottling/ActionThrottling.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$objectId of method AppBundle\\\\Security\\\\ActionThrottling\\\\Log\\:\\:setObjectId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/ActionThrottling/ActionThrottling.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$ip of method AppBundle\\\\Security\\\\ActionThrottling\\\\LogRepository\\:\\:removeLogs\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/ActionThrottling/ActionThrottling.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Security\\\\ActionThrottling\\\\LogRepository\\:\\:getApplicableLogs\\(\\) should return array\\{ip\\: int, object\\: int\\} but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/ActionThrottling/LogRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Security\\\\ActionThrottling\\\\LogRepository\\:\\:initMetadata\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/ActionThrottling/LogRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Security\\\\ActionThrottling\\\\LogRepository\\:\\:initMetadata\\(\\) should return M of CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata but returns CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/ActionThrottling/LogRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$databaseName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Metadata\\<object\\>\\:\\:setDatabase\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/ActionThrottling/LogRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Security\\\\GithubAuthenticator\\:\\:onAuthenticationSuccess\\(\\) has parameter \\$firewallName with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/GithubAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$githubId of method AppBundle\\\\Event\\\\Model\\\\GithubUser\\:\\:setGithubId\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/GithubAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$login of method AppBundle\\\\Event\\\\Model\\\\GithubUser\\:\\:setLogin\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/GithubAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$url of class Symfony\\\\Component\\\\HttpFoundation\\\\RedirectResponse constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/GithubAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/MembershipFeeVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Security\\\\MembershipFeeVoter extends generic class Symfony\\\\Component\\\\Security\\\\Core\\\\Authorization\\\\Voter\\\\Voter but does not specify its types\\: TAttribute, TSubject$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/MembershipFeeVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$compagnyId of method Afup\\\\Site\\\\Droits\\:\\:verifierDroitManagerPersonneMorale\\(\\) expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/MembershipFeeVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Security\\\\TalkVoter extends generic class Symfony\\\\Component\\\\Security\\\\Core\\\\Authorization\\\\Voter\\\\Voter but does not specify its types\\: TAttribute, TSubject$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/TalkVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Security\\\\TalkVoter\\:\\:supports\\(\\) has parameter \\$attribute with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/TalkVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$talk of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\SpeakerRepository\\:\\:getSpeakersByTalk\\(\\) expects AppBundle\\\\Event\\\\Model\\\\Talk, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/TalkVoter.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'id\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Security/TestGithubAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'login\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/TestGithubAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'name\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/TestGithubAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Security\\\\TestGithubAuthenticator\\:\\:getTestUsersDetails\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/TestGithubAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Security\\\\TestGithubAuthenticator\\:\\:onAuthenticationSuccess\\(\\) has parameter \\$firewallName with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/TestGithubAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$githubId of method AppBundle\\\\Event\\\\Model\\\\GithubUser\\:\\:setGithubId\\(\\) expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/TestGithubAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$login of method AppBundle\\\\Event\\\\Model\\\\GithubUser\\:\\:setLogin\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/TestGithubAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$name of method AppBundle\\\\Event\\\\Model\\\\GithubUser\\:\\:setName\\(\\) expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/TestGithubAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$url of class Symfony\\\\Component\\\\HttpFoundation\\\\RedirectResponse constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/TestGithubAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$userIdentifier of class Symfony\\\\Component\\\\Security\\\\Http\\\\Authenticator\\\\Passport\\\\Badge\\\\UserBadge constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Security/TestGithubAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Security/TestGithubAuthenticator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Article\\:\\:getChapeauFormate\\(\\) should return string but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Article.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Article\\:\\:getContenuFormate\\(\\) should return string but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Article.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/ArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\ArticleRepository\\:\\:countPublishedArticles\\(\\) has parameter \\$filtres with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/ArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\ArticleRepository\\:\\:createQueryBuilderPublishedActualites\\(\\) has parameter \\$filtres with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/ArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\ArticleRepository\\:\\:findAllPublishedArticles\\(\\) has parameter \\$filtres with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/ArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\ArticleRepository\\:\\:findAllPublishedArticles\\(\\) should return array\\<AppBundle\\\\Site\\\\Entity\\\\Article\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/ArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\ArticleRepository\\:\\:findArticleBySlug\\(\\) should return AppBundle\\\\Site\\\\Entity\\\\Article\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/ArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\ArticleRepository\\:\\:findBySlug\\(\\) should return AppBundle\\\\Site\\\\Entity\\\\Article\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/ArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\ArticleRepository\\:\\:findListForHome\\(\\) should return array\\<AppBundle\\\\Site\\\\Entity\\\\Article\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/ArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\ArticleRepository\\:\\:findNext\\(\\) should return AppBundle\\\\Site\\\\Entity\\\\Article\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/ArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\ArticleRepository\\:\\:findPrevious\\(\\) should return AppBundle\\\\Site\\\\Entity\\\\Article\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/ArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\ArticleRepository\\:\\:findPublishedArticles\\(\\) has parameter \\$filtres with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/ArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\ArticleRepository\\:\\:findPublishedArticles\\(\\) should return array\\<AppBundle\\\\Site\\\\Entity\\\\Article\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/ArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\ArticleRepository\\:\\:getAllYears\\(\\) should return array\\<int\\> but returns list\\<mixed\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/ArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\ArticleRepository\\:\\:getEventsLabelsById\\(\\) should return array\\<int, string\\> but returns array\\<mixed\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/ArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function array_map expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/ArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\CountryRepository\\:\\:getAllSortedByName\\(\\) should return array\\<AppBundle\\\\Site\\\\Entity\\\\Country\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/CountryRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\FeuilleRepository\\:\\:getAllFeuilles\\(\\) should return array\\<AppBundle\\\\Site\\\\Entity\\\\Feuille\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/FeuilleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\FeuilleRepository\\:\\:getFeuillesEnfant\\(\\) should return array\\<AppBundle\\\\Site\\\\Entity\\\\Feuille\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/FeuilleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Site\\\\Entity\\\\Repository\\\\RubriqueRepository\\:\\:getAllRubriques\\(\\) should return array\\<AppBundle\\\\Site\\\\Entity\\\\Rubrique\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Entity/Repository/RubriqueRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Site\\\\Form\\\\ArticleType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Form/ArticleType.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type string\\|null\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Form/ArticleType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Site\\\\Form\\\\FeuilleType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Form/FeuilleType.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type string\\|null\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Form/FeuilleType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Site\\\\Form\\\\NewsFiltersType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Form/NewsFiltersType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Site\\\\Form\\\\RubriqueType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Site/Form/RubriqueType.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type string\\|null\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Site/Form/RubriqueType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Slack\\\\Field\\:\\:setValue\\(\\) has parameter \\$value with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/Field.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'error\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/LegacyClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'ok\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/LegacyClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Slack\\\\LegacyClient\\:\\:__construct\\(\\) has parameter \\$token with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/LegacyClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Slack\\\\LegacyClient\\:\\:invite\\(\\) has parameter \\$email with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/LegacyClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/LegacyClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'talks\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/MessageFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method diff\\(\\) on DateTime\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/MessageFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getPrettyName\\(\\) on AppBundle\\\\Event\\\\Model\\\\TicketType\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/MessageFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTitle\\(\\) on AppBundle\\\\Event\\\\Model\\\\Talk\\|null\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Slack/MessageFactory.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Call to an undefined method object\\:\\:modify\\(\\)\\.$#',
	'identifier' => 'method.notFound',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'deleted\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'email\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'is_admin\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'name\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'next_cursor\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'profile\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'real_name\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getLastSubscription\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot clone non\\-object variable \\$lastSubscription of type mixed\\.$#',
	'identifier' => 'clone.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Slack\\\\UsersChecker\\:\\:checkUsersValidity\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$cursor of method AppBundle\\\\Slack\\\\UsersClient\\:\\:loadPage\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersChecker.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'error\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'ok\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Slack\\\\UsersClient\\:\\:loadPage\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Slack\\\\UsersClient\\:\\:loadPage\\(\\) should return array but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Slack/UsersClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyOembedClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$subject of function preg_replace expects array\\<float\\|int\\|string\\>\\|string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyOembedClient.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type mixed supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyPolyfill.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'end\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyPolyfill.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'match\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyPolyfill.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'start\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyPolyfill.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyPolyfill.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\SocialNetwork\\\\Bluesky\\\\BlueskyPolyfill\\:\\:getMatchAndPosition\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyPolyfill.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\SocialNetwork\\\\Bluesky\\\\BlueskyPolyfill\\:\\:parseFacets\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyPolyfill.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$needle of method Symfony\\\\Component\\\\String\\\\AbstractString\\:\\:indexOf\\(\\) expects array\\<string\\>\\|string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyPolyfill.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of class Symfony\\\\Component\\\\String\\\\ByteString constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyPolyfill.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyPolyfill.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\SocialNetwork\\\\Bluesky\\\\BlueskyTransport\\:\\:buildEmbed\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyTransport.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\SocialNetwork\\\\Bluesky\\\\BlueskyTransport\\:\\:buildThumbnail\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyTransport.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\SocialNetwork\\\\Bluesky\\\\BlueskyTransport\\:\\:buildThumbnail\\(\\) should return array\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyTransport.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$uri of method AppBundle\\\\SocialNetwork\\\\Bluesky\\\\BlueskyTransport\\:\\:extractPostId\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyTransport.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of class AppBundle\\\\SocialNetwork\\\\StatusId constructor expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SocialNetwork/Bluesky/BlueskyTransport.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\SpeakerInfos\\\\Form\\\\HotelReservationType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/Form/HotelReservationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$object of static method DateTimeImmutable\\:\\:createFromMutable\\(\\) expects DateTime, DateTime\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/Form/HotelReservationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of function count expects array\\|Countable, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/Form/HotelReservationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$haystack of function in_array expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/Form/HotelReservationType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\SpeakerInfos\\\\Form\\\\SpeakersContactType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/Form/SpeakersContactType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\SpeakerInfos\\\\Form\\\\SpeakersDinerType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/Form/SpeakersDinerType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\SpeakerInfos\\\\Form\\\\SpeakersExpensesType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/Form/SpeakersExpensesType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\SpeakerInfos\\\\Form\\\\SpeakersMicrophoneType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/Form/SpeakersMicrophoneType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\SpeakerInfos\\\\Form\\\\TravelSponsorType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/Form/TravelSponsorType.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\." between non\\-falsy\\-string and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/SpeakersExpensesStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method format\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/SpeakersExpensesStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getDateStart\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/SpeakersExpensesStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getId\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/SpeakersExpensesStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot call method getTitle\\(\\) on mixed\\.$#',
	'identifier' => 'method.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/SpeakersExpensesStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\SpeakerInfos\\\\SpeakersExpensesStorage\\:\\:buildFilename\\(\\) has parameter \\$file with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/SpeakersExpensesStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\SpeakerInfos\\\\SpeakersExpensesStorage\\:\\:cleanFiles\\(\\) has parameter \\$duration with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/SpeakersExpensesStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\SpeakerInfos\\\\SpeakersExpensesStorage\\:\\:delete\\(\\) has parameter \\$filename with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/SpeakersExpensesStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\SpeakerInfos\\\\SpeakersExpensesStorage\\:\\:getFiles\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/SpeakersExpensesStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$duration of class DateInterval constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/SpeakersExpensesStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/SpeakersExpensesStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/SpeakersExpensesStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#4 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SpeakerInfos/SpeakersExpensesStorage.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#4 \\$parameters of class Presta\\\\SitemapBundle\\\\Sitemap\\\\Url\\\\GoogleVideo constructor expects array\\{content_location\\?\\: string, player_location\\?\\: string, player_location_allow_embed\\?\\: string, player_location_autoplay\\?\\: string, duration\\?\\: int, expiration_date\\?\\: DateTimeInterface, rating\\?\\: float\\|int, view_count\\?\\: int, \\.\\.\\.\\}, array\\{player_location\\: string\\|null\\} given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Subscriber/SitemapXmlSubscriber.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Subtitles\\\\Parser\\:\\:parse\\(\\) has parameter \\$contentSrt with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Subtitles/Parser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$_str of method Captioning\\\\File\\:\\:loadFromString\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Subtitles/Parser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function trim expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Subtitles/Parser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\SuperApero\\\\Entity\\\\Repository\\\\SuperAperoRepository\\:\\:findActive\\(\\) should return AppBundle\\\\SuperApero\\\\Entity\\\\SuperApero\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SuperApero/Entity/Repository/SuperAperoRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\SuperApero\\\\Entity\\\\Repository\\\\SuperAperoRepository\\:\\:findOneByYear\\(\\) should return AppBundle\\\\SuperApero\\\\Entity\\\\SuperApero\\|null but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SuperApero/Entity/Repository/SuperAperoRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\SuperApero\\\\Entity\\\\Repository\\\\SuperAperoRepository\\:\\:getAllSortedByYear\\(\\) should return array\\<AppBundle\\\\SuperApero\\\\Entity\\\\SuperApero\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SuperApero/Entity/Repository/SuperAperoRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\SuperApero\\\\Form\\\\SuperAperoMeetupType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SuperApero/Form/SuperAperoMeetupType.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\SuperApero\\\\Form\\\\SuperAperoType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/SuperApero/Form/SuperAperoType.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'@type\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/DataExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'articleBody\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/DataExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'datePublished\' on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/DataExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset \'host\' on array\\{scheme\\?\\: string, host\\?\\: string, port\\?\\: int\\<0, 65535\\>, user\\?\\: string, pass\\?\\: string, path\\?\\: string, query\\?\\: string, fragment\\?\\: string\\}\\|false\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/DataExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/DataExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\TechLetter\\\\DataExtractor\\:\\:extractDataForTechLetter\\(\\) has parameter \\$url with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/DataExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$datetime of class DateTimeImmutable constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/DataExtractor.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\TechLetter\\\\Form\\\\SendingType extends generic class Symfony\\\\Component\\\\Form\\\\AbstractType but does not specify its types\\: TData$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/Form/SendingType.php',
];
$ignoreErrors[] = [
	'message' => '#^Argument of an invalid type DOMNodeList\\<DOMNameSpaceNode\\|DOMNode\\>\\|false supplied for foreach, only iterables are supported\\.$#',
	'identifier' => 'foreach.nonIterable',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/HtmlParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\TechLetter\\\\HtmlParser\\:\\:__construct\\(\\) has parameter \\$html with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/HtmlParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\TechLetter\\\\HtmlParser\\:\\:getMeta\\(\\) has parameter \\$name with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/HtmlParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\TechLetter\\\\HtmlParser\\:\\:getStandardMeta\\(\\) has parameter \\$name with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/HtmlParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$meta of method AppBundle\\\\TechLetter\\\\HtmlParser\\:\\:getSocialMeta\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/HtmlParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$source of method DOMDocument\\:\\:loadHTML\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/HtmlParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\TechLetter\\\\HtmlParser\\:\\:\\$meta with generic class DOMNodeList does not specify its types\\: TNode$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/HtmlParser.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\TechLetter\\\\MailchimpSynchronizer\\:\\:getSubscribedEmailsOnMailchimp\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/MailchimpSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\TechLetter\\\\MailchimpSynchronizer\\:\\:getSubscribedEmailsOnWebsite\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/MailchimpSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\TechLetter\\\\MailchimpSynchronizer\\:\\:subscribeAddresses\\(\\) has parameter \\$emails with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/MailchimpSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\TechLetter\\\\MailchimpSynchronizer\\:\\:unsubscribeAddresses\\(\\) has parameter \\$emails with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/MailchimpSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_diff expects an array of values castable to string, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/MailchimpSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$arrays of function array_diff expects an array of values castable to string, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/MailchimpSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$email of method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:subscribeAddressWithoutConfirmation\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/MailchimpSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$email of method AppBundle\\\\Mailchimp\\\\Mailchimp\\:\\:unSubscribeAddress\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/MailchimpSynchronizer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\TechLetter\\\\Model\\\\Article\\:\\:jsonSerialize\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/Model/Article.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\TechLetter\\\\Model\\\\News\\:\\:jsonSerialize\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/Model/News.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\TechLetter\\\\Model\\\\Project\\:\\:jsonSerialize\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/Model/Project.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\TechLetter\\\\Model\\\\TechLetter\\:\\:jsonSerialize\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/Model/TechLetter.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\TechLetter\\\\UrlCrawler\\:\\:crawlUrl\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/UrlCrawler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\TechLetter\\\\UrlCrawler\\:\\:crawlUrl\\(\\) has parameter \\$url with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/UrlCrawler.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$url of function curl_init expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/TechLetter/UrlCrawler.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Ting\\\\DateTimeWithTimeZoneSerializer\\:\\:unserialize\\(\\) has parameter \\$options with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Ting/DateTimeWithTimeZoneSerializer.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$timezone of class DateTimeZone constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Ting/DateTimeWithTimeZoneSerializer.php',
];
$ignoreErrors[] = [
	'message' => '#^Class AppBundle\\\\Ting\\\\HydratorAggregator extends generic class CCMBenchmark\\\\Ting\\\\Repository\\\\Hydrator but does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Ting/HydratorAggregator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Ting\\\\HydratorAggregator\\:\\:finalizeAggregate\\(\\) has parameter \\$aggregate with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Ting/HydratorAggregator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Ting\\\\HydratorAggregator\\:\\:finalizeAggregate\\(\\) has parameter \\$result with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Ting/HydratorAggregator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Ting\\\\HydratorAggregator\\:\\:finalizeAggregate\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Ting/HydratorAggregator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Ting\\\\HydratorAggregator\\:\\:finalizeAggregate\\(\\) should return array but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Ting/HydratorAggregator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$connectionName of method CCMBenchmark\\\\Ting\\\\Repository\\\\Hydrator\\<mixed\\>\\:\\:hydrateColumns\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Ting/HydratorAggregator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$result of method AppBundle\\\\Ting\\\\HydratorAggregator\\:\\:finalizeAggregate\\(\\) expects array, array\\<int, mixed\\>\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Ting/HydratorAggregator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$database of method CCMBenchmark\\\\Ting\\\\Repository\\\\Hydrator\\<mixed\\>\\:\\:hydrateColumns\\(\\) expects string, string\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Ting/HydratorAggregator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$columns of method CCMBenchmark\\\\Ting\\\\Repository\\\\Hydrator\\<mixed\\>\\:\\:hydrateColumns\\(\\) expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Ting/HydratorAggregator.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Ting/HydratorAggregator.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access offset string on mixed\\.$#',
	'identifier' => 'offsetAccess.nonOffsetAccessible',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Ting/JoinHydrator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$array of function array_filter expects array, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Ting/JoinHydrator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$string of function substr expects string, string\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Twig/AssetsExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access property \\$urlName on AppBundle\\\\Antennes\\\\Meetup\\|null\\.$#',
	'identifier' => 'property.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Twig/OfficesExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$code of method AppBundle\\\\Antennes\\\\AntenneRepository\\:\\:findByCode\\(\\) expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 3,
	'path' => __DIR__ . '/sources/AppBundle/Twig/OfficesExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$url of function curl_init expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Twig/TwigExtension.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Twig\\\\ViewRenderer\\:\\:render\\(\\) has parameter \\$parameters with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Twig/ViewRenderer.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Validator\\\\Constraints\\\\UniqueEntity\\:\\:__construct\\(\\) has parameter \\$repository with generic class CCMBenchmark\\\\Ting\\\\Repository\\\\Repository but does not specify its types\\: T$#',
	'identifier' => 'missingType.generics',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Validator/Constraints/UniqueEntity.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot access constant class on mixed\\.$#',
	'identifier' => 'classConstant.nonObject',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Validator/Constraints/UniqueEntityValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Validator\\\\Constraints\\\\UniqueEntityValidator\\:\\:validate\\(\\) has parameter \\$entity with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Validator/Constraints/UniqueEntityValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$repositoryName of method CCMBenchmark\\\\Ting\\\\Repository\\\\RepositoryFactory\\:\\:get\\(\\) expects class\\-string\\<CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\<object\\>\\>, CCMBenchmark\\\\Ting\\\\Repository\\\\Repository\\|string given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Validator/Constraints/UniqueEntityValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$array of function implode expects array\\<string\\>, array\\<string, mixed\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Validator/Constraints/UniqueEntityValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\.\\.\\.\\$values of function sprintf expects bool\\|float\\|int\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Validator/Constraints/UniqueEntityValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Unable to resolve the template type R in call to method CCMBenchmark\\\\Ting\\\\Repository\\\\RepositoryFactory\\:\\:get\\(\\)$#',
	'identifier' => 'argument.templateType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Validator/Constraints/UniqueEntityValidator.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Veille\\\\Entity\\\\Repository\\\\EnvoiRepository\\:\\:getAllOrderedByDateDesc\\(\\) should return list\\<AppBundle\\\\Veille\\\\Entity\\\\Envoi\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Veille/Entity/Repository/EnvoiRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Veille\\\\Entity\\\\Repository\\\\EnvoiRepository\\:\\:getAllPreviouslySent\\(\\) should return list\\<AppBundle\\\\Veille\\\\Entity\\\\Envoi\\> but returns mixed\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Veille/Entity/Repository/EnvoiRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Veille\\\\Entity\\\\Repository\\\\NewsletterDesinscriptionRepository\\:\\:createFromWebhookData\\(\\) has parameter \\$data with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Veille/Entity/Repository/NewsletterDesinscriptionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Veille\\\\Entity\\\\NewsletterDesinscription\\:\\:\\$email \\(string\\|null\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Veille/Entity/Repository/NewsletterDesinscriptionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Veille\\\\Entity\\\\NewsletterDesinscription\\:\\:\\$mailchimpId \\(string\\|null\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Veille/Entity/Repository/NewsletterDesinscriptionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Property AppBundle\\\\Veille\\\\Entity\\\\NewsletterDesinscription\\:\\:\\$raison \\(string\\|null\\) does not accept mixed\\.$#',
	'identifier' => 'assign.propertyType',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Veille/Entity/Repository/NewsletterDesinscriptionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/Veille/Entity/Repository/NewsletterInscriptionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Veille\\\\Entity\\\\Repository\\\\NewsletterInscriptionRepository\\:\\:getAllSubscriptionsWithUser\\(\\) should return list\\<array\\{login\\: string, email\\: string, nom\\: string, prenom\\: string, lastsubscription\\: string, subscription_date\\: string, id\\: int, user_id\\: int\\}\\> but returns list\\<array\\<string, mixed\\>\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Veille/Entity/Repository/NewsletterInscriptionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\Veille\\\\Entity\\\\Repository\\\\NewsletterInscriptionRepository\\:\\:getSubscribedEmails\\(\\) should return list\\<array\\{email\\: string\\}\\> but returns list\\<array\\<string, mixed\\>\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/Veille/Entity/Repository/NewsletterInscriptionRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$talkId of class AppBundle\\\\VideoNotifier\\\\HistoryEntry constructor expects int, int\\|null given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/VideoNotifier/Engine.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$talkIds of method AppBundle\\\\Event\\\\Model\\\\Repository\\\\TalkRepository\\:\\:findList\\(\\) expects array\\<int\\>, list\\<int\\|null\\> given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/VideoNotifier/Engine.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type int\\|null\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 2,
	'path' => __DIR__ . '/sources/AppBundle/VideoNotifier/Engine.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\VideoNotifier\\\\HistoryRepository\\:\\:getNumberOfStatusesPerTalk\\(\\) should return array\\<int, int\\> but returns array\\<mixed\\>\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/VideoNotifier/HistoryRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Possibly invalid array key type mixed\\.$#',
	'identifier' => 'offsetAccess.invalidOffset',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/VideoNotifier/HistoryRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method AppBundle\\\\VideoNotifier\\\\StatusGenerator\\:\\:generate\\(\\) has parameter \\$speakers with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/VideoNotifier/StatusGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$speakers of method AppBundle\\\\VideoNotifier\\\\StatusGenerator\\:\\:buildMentionsText\\(\\) expects array\\<AppBundle\\\\Event\\\\Model\\\\Speaker\\>, array given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/AppBundle/VideoNotifier/StatusGenerator.php',
];
$ignoreErrors[] = [
	'message' => '#^Binary operation "\\*" between mixed and mixed results in an error\\.$#',
	'identifier' => 'binaryOp.invalid',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to int\\.$#',
	'identifier' => 'cast.int',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Cannot cast mixed to string\\.$#',
	'identifier' => 'cast.string',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedArticleRepository\\:\\:findIdByKey\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedArticleRepository\\:\\:findIdByKey\\(\\) has parameter \\$key with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedArticleRepository\\:\\:findLatest\\(\\) has parameter \\$format with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedArticleRepository\\:\\:findLatest\\(\\) has parameter \\$nombre with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedArticleRepository\\:\\:findLatest\\(\\) has parameter \\$page with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedArticleRepository\\:\\:hydrate\\(\\) has parameter \\$rows with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedArticleRepository\\:\\:hydrate\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedArticleRepository\\:\\:hydrateDisplayable\\(\\) has parameter \\$format with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedArticleRepository\\:\\:hydrateDisplayable\\(\\) has parameter \\$rows with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedArticleRepository\\:\\:insert\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedArticleRepository\\:\\:isRelevant\\(\\) has parameter \\$content with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedArticleRepository\\:\\:save\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedArticleRepository\\:\\:search\\(\\) should return array\\<PlanetePHP\\\\FeedArticle\\> but returns array\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedArticleRepository\\:\\:update\\(\\) has no return type specified\\.$#',
	'identifier' => 'missingType.return',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedArticleRepository\\:\\:update\\(\\) has parameter \\$id with no type specified\\.$#',
	'identifier' => 'missingType.parameter',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(mixed\\)\\: mixed\\)\\|null, Closure\\(array\\)\\: PlanetePHP\\\\DisplayableFeedArticle given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(mixed\\)\\: mixed\\)\\|null, Closure\\(array\\)\\: PlanetePHP\\\\FeedArticle given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$format of function date expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of class PlanetePHP\\\\FeedArticle constructor expects int\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$title of class PlanetePHP\\\\DisplayableFeedArticle constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of function intval expects array\\|bool\\|float\\|GMP\\|int\\|resource\\|SimpleXMLElement\\|string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 2,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#10 \\$status of class PlanetePHP\\\\FeedArticle constructor expects int\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$feedId of class PlanetePHP\\\\FeedArticle constructor expects int\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$url of class PlanetePHP\\\\DisplayableFeedArticle constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$key of class PlanetePHP\\\\FeedArticle constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#4 \\$author of class PlanetePHP\\\\DisplayableFeedArticle constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#4 \\$title of class PlanetePHP\\\\FeedArticle constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#5 \\$content of class PlanetePHP\\\\DisplayableFeedArticle constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#5 \\$url of class PlanetePHP\\\\FeedArticle constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#6 \\$feedName of class PlanetePHP\\\\DisplayableFeedArticle constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#6 \\$update of class PlanetePHP\\\\FeedArticle constructor expects int\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#7 \\$author of class PlanetePHP\\\\FeedArticle constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#7 \\$feedUrl of class PlanetePHP\\\\DisplayableFeedArticle constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#8 \\$summary of class PlanetePHP\\\\FeedArticle constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#9 \\$content of class PlanetePHP\\\\FeedArticle constructor expects string\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedArticleRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedRepository\\:\\:find\\(\\) should return array\\<PlanetePHP\\\\Feed\\> but returns array\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedRepository\\:\\:findActive\\(\\) should return array\\<PlanetePHP\\\\Feed\\> but returns array\\.$#',
	'identifier' => 'return.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedRepository\\:\\:hydrate\\(\\) has parameter \\$row with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedRepository\\:\\:hydrateAll\\(\\) has parameter \\$rows with no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Method PlanetePHP\\\\FeedRepository\\:\\:hydrateAll\\(\\) return type has no value type specified in iterable type array\\.$#',
	'identifier' => 'missingType.iterableValue',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$callback of function array_map expects \\(callable\\(mixed\\)\\: mixed\\)\\|null, Closure\\(array\\)\\: PlanetePHP\\\\Feed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$id of class PlanetePHP\\\\Feed constructor expects int, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$row of method PlanetePHP\\\\FeedRepository\\:\\:hydrate\\(\\) expects array, array\\<string, mixed\\>\\|false given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#1 \\$value of static method PlanetePHP\\\\FeedStatus\\:\\:from\\(\\) expects int\\|string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#2 \\$name of class PlanetePHP\\\\Feed constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#3 \\$url of class PlanetePHP\\\\Feed constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#4 \\$feed of class PlanetePHP\\\\Feed constructor expects string, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedRepository.php',
];
$ignoreErrors[] = [
	'message' => '#^Parameter \\#6 \\$userId of class PlanetePHP\\\\Feed constructor expects int\\|null, mixed given\\.$#',
	'identifier' => 'argument.type',
	'count' => 1,
	'path' => __DIR__ . '/sources/PlanetePHP/FeedRepository.php',
];

return ['parameters' => ['ignoreErrors' => $ignoreErrors]];
