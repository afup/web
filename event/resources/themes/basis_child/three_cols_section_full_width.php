<?php

$default = 1;
$min = 1;
$max = 20;

$nthChild = $default;

if (isset($_GET['nth-child']) && $_GET['nth-child'] >= $min && $_GET['nth-child'] <= $max) {
    $nthChild = $_GET['nth-child'];
}

$css = <<<CSS
.product-content-wrapper > div > section:nth-child($nthChild) {
    padding: 1.2rem 3.7rem;
    margin: 2rem auto;
    max-width: 100%;
}
CSS;

header("Content-type: text/css");
echo $css;

