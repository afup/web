<?php
/*
 * This work is hereby released into the Public Domain.
 * To view a copy of the public domain dedication,
 * visit http://creativecommons.org/licenses/publicdomain/ or send a letter to
 * Creative Commons, 559 Nathan Abbott Way, Stanford, California 94305, USA.
 *
 */

require_once "Pie.class.php";


$graph = new Graph(400, 300);
$graph->setTiming(TRUE);
$graph->setAntiAliasing(TRUE);

$graph->title->set("It's raining again");
$graph->title->setBackgroundColor(new White(25));
$graph->title->border->show();
$graph->title->setPadding(3, 3, 3, 3);

$x = array();

for($i = 0; $i < 7; $i++) {
	$x[] = mt_rand(20, 100);
}

$y = array(
	'Lundi',
	'Mardi',
	'Mercredi',
	'Jeudi',
	'Vendredi',
	'Samedi',
	'Dimanche'
);

$plot = new Pie($x, PIE_AQUA);
$plot->setCenter(0.5, 0.58);
$plot->setSize(mt_rand(50, 100) / 100, mt_rand(50, 100) / 100);

$plot->setLegend($y);
$plot->setBorder(new Color(0, 0, 0));

$plot->label->hide(TRUE);


//$plot->legend->add($plot, "Test", LEGEND_BACKGROUND);
$plot->legend->setPadding(10, 10, 10, 10);
$plot->legend->setTextMargin(8, 0);
$plot->legend->shadow->setSize(4);

$graph->add($plot);
$graph->draw();
?>