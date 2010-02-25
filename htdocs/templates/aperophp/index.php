<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="keywords" content="apéros PHP" lang="fr" />
    <link rel="stylesheet" href="<?php echo $this->template ?>/templates/aperophp/style.css" type="text/css" media="all" />
    <title>ApéroPHP...</title>
  </head>
  <body>
    <div id="en-avant">
      <h1>les prochains apéros</h1>

      <?php if ($this->aperos): ?>
      <?php foreach ($this->aperos as $apero): ?>
      <div class="apero-prevu">
        <h2><?php echo $apero['apero']->ville ?></h2>
        <p class="infos"><?php echo $apero['apero']->date ?><br />
        <?php echo $apero['apero']->lieu ?></p>
        <p>organisé par <?php echo $apero['responsable']->prenom . ' ' . $apero['responsable']->nom ?><br />
        déjà 3 présents, <a href="">j'y serais aussi</a>.</p>
      </div>
      <?php endforeach ?>
      <?php else: ?>
      <div class="apero-prevu">
        <h2>Null part</h2>
        <p class="infos">pas d'apéro prévu en ce moment.</p>
        <p>C'est le temps d'en <a href="nouvel-apero.php">créer un</a>.</p>
      </div>
      <?php endif ?>

      <div class="apero-en-action">
        <h2>Nouveau</h2>
        <p>Ajouter <a href="">un nouvel apéro</a>.</p>
      </div>

    </div>

    <div id="plus-loin">
      <h1>apérophp.net</h1>

	<p>Un apéro php, l'occasion de rencontrer,<br />
	de boire un coup,<br />
	et <em>accessoirement</em><br />
	de parler de l'univers PHP ou du web,<br />
	de la mort des dinosaures ou du <em>recherche google</em>.</p>

	<p>Pour participer, il suffit de s'inscrire.</p>

	<p>Pleins d'apéros ont déjà eu lieu un peut partout en France,
	et aussi à l'étranger (Suisse, Belgique). Les archives sont
	disponibles et des photos aussi.</p>

	<p>Si vous avez envie de lancer votre premier apéro,
	voici quelques astuces pour bien le réussir.</p>
    </div>
  </body>
</html>