<?php
if ($active_var==TRUE)
{

echo "<hr>Debug<br>";
echo "<pre>";
print_r ($_SESSION);
echo "<br>";
print_r ($_GET);
echo "<br>";
print_r ($_POST);

echo "</pre>";
}
?>
