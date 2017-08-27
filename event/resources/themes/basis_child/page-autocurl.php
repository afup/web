<?php
/**
 * Template Name: Curl Template
 *
 * @package Basis
 */
?>
<?php
$content = 'Une erreur est survenue';
$curl_url = str_replace('..', '', get_query_var('url'));
var_dump($curl_url);
var_dump($_SERVER);
die;
if ($curl_url !== '') {
	$lang = 'fr';
	if (isset($_GET['lang']) && isset($curl_url[$_GET['lang']])) {
		$lang = $_GET['lang'];
	}
	remove_filter('the_content', 'wpautop');
	$ch = curl_init();
	curl_setopt(
		$ch,
		CURLOPT_URL,
		'http://afup.dev/pages/event/' . $curl_url
	);

	curl_setopt($ch, CURLOPT_HEADER, TRUE);
	curl_setopt(
		$ch,
		CURLOPT_REFERER,
		'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
	);


	/* Domains to use when rewriting some headers. */
	$remoteDomain = 'afup.dev';
	$proxyDomain = 'event.afup.dev';

	/*print_r(file_get_contents('php://input'));*/
	if (isset($_POST)) {
		curl_setopt($ch, CURLOPT_POST, 1);
		$post = $_POST;
		if (isset($_FILES)) {
			foreach ($_FILES as $name => $file) {
				if ($file['size'] !== 0) {
					$post[$name] = new \CURLFile($file['tmp_name'], $file['type'], $file['name']);
				}
			}
		}

		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
	}

	$headers = getallheaders();

	/* Translate some headers to make the remote party think we actually browsing that site. */
	$extraHeaders = array();
	if (isset($headers['Referer']))
	{
		$extraHeaders[] = 'Referer: '. str_replace($proxyDomain, $remoteDomain, $headers['Referer']);
	}
	if (isset($headers['Origin']))
	{
		$extraHeaders[] = 'Origin: '. str_replace($proxyDomain, $remoteDomain, $headers['Origin']);
	}

	/* Forward cookie as it came.  */
	curl_setopt($ch, CURLOPT_HTTPHEADER, $extraHeaders);
	if (isset($headers['Cookie']))
	{
		curl_setopt($ch, CURLOPT_COOKIE, $headers['Cookie']);
	}

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$response = curl_exec($ch);
	$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
	$headers = substr($response, 0, $header_size);
	$body = substr($response, $header_size);

	$headerArray = explode(PHP_EOL, $headers);

	/* Process response headers. */
	foreach($headerArray as $header)
	{
		$colonPos = strpos($header, ':');
		if ($colonPos !== FALSE)
		{
			$headerName = substr($header, 0, $colonPos);

			/* Ignore content headers, let the webserver decide how to deal with the content. */
			if (trim($headerName) == 'Content-Encoding') continue;
			if (trim($headerName) == 'Content-Length') continue;
			if (trim($headerName) == 'Transfer-Encoding') continue;
			if (trim($headerName) == 'Location') continue;
			/* -- */
			/* Change cookie domain for the proxy */
			if (trim($headerName) == 'Set-Cookie')
			{
				$header = str_replace('domain='.$remoteDomain, 'domain='.$proxyDomain, $header);
			}
			/* -- */

		}
		header($header, FALSE);
	}



	if (curl_errno($ch) == 0) {
		$content = $body;
	}

}
?>

<?php
function basis_child_body_classes() {return ['page-template-default']; }
add_filter( 'body_class', 'basis_child_body_classes' );

get_header();

echo $content;

get_footer();
