<?php

declare(strict_types=1);

ob_implicit_flush(true);
@ignore_user_abort(true);

$img = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAEALAAAAAABAAEAAAIBTAA7');
header('Content-Type: image/gif');
header('Content-Length: ' . strlen($img));
header('Connection: Close');
echo $img;

ob_implicit_flush(false);
ob_start();

$robot = __DIR__ . "/../../../robots/planete/explorateur.php";
if (fileatime($robot) < time() - 3600) {
    require($robot);
}
