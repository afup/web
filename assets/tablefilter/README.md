# TableFilter 0.7.2

Copie de [TableFilter](https://github.com/koalyptus/TableFilter) 0.7.2 (licence MIT, voir `LICENSE`), prise dans l'archive npm `tablefilter-0.7.2.tgz` (`dist/tablefilter/`). Version figée volontairement : les versions 0.7.4 et suivantes (octobre 2026) changent l'outillage de build et n'ont pas encore assez de recul.

Fichiers repris : `tablefilter.js`, `style/tablefilter.css`, le thème `transparent` et les images référencées par ces deux CSS. Le chunk `tf-1-*.js` (extensions) n'est pas repris : aucune extension n'est utilisée.

Modification : la règle `.ezModifiedCell` est retirée de `style/tablefilter.css`, car elle référence `themes/bg_mod_cell.png`, absente de l'archive.

L'initialisation des tableaux de l'admin est dans `assets/admin/tableaux-filtrables.js`. Les URL versionnées des CSS lui sont passées par les attributs `data-` de sa balise `<script>` : le thème via l'option `themes[].path`, la feuille principale en surchargeant `getStylesheetPath()`, car la 0.7.2 ignore l'option `stylesheet` (le getter lit `this.config`, qui est une méthode).
