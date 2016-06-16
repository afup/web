<?php
namespace Afup\Site\Utils;

class Web {
    function mettreAJour($update = false) {
        if ($update === true) {
            // Direction le dossier avec le repo git
            $command = "cd ".$GLOBALS['conf']->obtenir('git|local_repo').";";
            // Si modif local, on la stocke temporairement pour éviter les conflits
            $command .= "git stash;";
            // On récupère les éventuelles nouvelles branches distantes
            $command .= "git fetch";
            // On track la branche souhaitée dans une branche locale (même si déjà fait)
            $command .= "git branch --track branch-name origin/".$GLOBALS['conf']->obtenir('git|deployed_branch')." 2> /dev/null;";
            // On se déplace sur la branche souhaitée
            $command .= "git checkout ".$GLOBALS['conf']->obtenir('git|deployed_branch').";";
            // On la met à jour
            $command .= "git pull origin;";
            // On réapplique les modifs locales
            $command .= "git stash pop;";

            // On detecte tous les liens symboliques dans le repertoire de destination
            // Et on rsync en ignorant ces fichiers grace a l'entree standard
            $command .= 'find /' . $GLOBALS['conf']->obtenir('git|local_export') . ' -type l  -printf "/%P*\n" | rsync -rvcC --safe-links --delete ./ /' . $GLOBALS['conf']->obtenir('git|local_export') . ' --exclude-from=- ; ';
            // Direction le dossier exporté
            $command .= "cd /".$GLOBALS['conf']->obtenir('git|local_export').";";
            // Nettoyage cache TWIG
            $command .= "rm -rf htdocs/tmp/twig;" ;
            // MAJ composer
            $command .= "composer install --no-dev;";
            // Post to slack
            $command .= "curl -X POST --data-urlencode 'payload={\"channel\": \"#outils\", \"text\": \"We just updated the website!\n<http://www.afup.org|Go check it out !>\"}' https://hooks.slack.com/services/".$GLOBALS['conf']->obtenir('slack|token');
            opcache_reset();
            $output = shell_exec($command);
            opcache_reset();
            return ['result' => true, 'output' => $output];
        } else {
            return ['result' => false, 'output' => null];
        }
    }
}
