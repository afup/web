<?php
ob_start();
phpinfo();
$phpinfo = ob_get_contents();
ob_end_clean();
$phpinfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $phpinfo);

$smarty->assign('phpinfo', $phpinfo);