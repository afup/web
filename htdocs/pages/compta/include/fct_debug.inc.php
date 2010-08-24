<?php
assert_options(ASSERT_ACTIVE, $active_assert);
assert_options(ASSERT_WARNING, 0);
assert_options(ASSERT_QUIET_EVAL, 1);


function recup_assert($file, $line, $code)
{
    echo "<hr />";
    echo "Erreur : <br />";
    echo "Fichier '$file'<br />";
    echo "Line '$line'<br />";
    echo "Code '$code'<br />";
	echo "<hr />";
}

assert_options (ASSERT_CALLBACK, 'recup_assert');
?>
