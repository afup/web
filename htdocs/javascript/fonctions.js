function creerLogin(nom, prenom) {  
    return modifNom(prenom) + modifNom(nom);
}

// Donne le focus à un élément
function donnerFocus(id) {
    if (document.getElementById) {
        var element = document.getElementById(id);
        if (element != null) {
            element.focus();
        }
    }
}

function modifNom(nom) {
    nom = NettoieNom(nom);
    mots = explode(' ', nom);
    mots = array_map(ucfirst, mots);
    
    return implode('', mots);
}

function NettoieNom(nom) {
    var lettres = {
        chercher : [
        'à', 'À', 'â', 'Â', 'æ', 'Æ', 
        'ç', 'Ç', 
        'é', 'É', 'è', 'È', 'ê', 'Ê', 'ë', 'Ë', 
        'î', 'Î', 'ï', 'Ï', 
        'ñ', 'Ñ', 
        'ô', 'Ô', 'œ', 'Œ', 
        'ù', 'Ù', 'û', 'Û', 'ü', 'Ü', 
        'ÿ', 'Ÿ', 
        '-', ],
        remplacer: [
        'a', 'a', 'a', 'a', 'ae', 'ae', 
        'c', 'c', 
        'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 
        'i', 'i', 'i', 'i', 
        'n', 'n', 
        'o', 'o', 'oe', 'oe', 
        'u', 'u', 'u', 'u', 'u', 'u', 
        'y', 'y', 
        ' ', ]
    };
    
    return str_replace(lettres.chercher, lettres.remplacer, nom);
}

// Empêche de soumettre un formulaire plusieurs fois
function soumettreUneSeuleFois(formulaire) {
    if (formulaire.elements['soumettre'] != null) {
        if (formulaire.elements['soumettre'].disabled) {
            return false;
        }
        formulaire.elements['soumettre'].disabled = true;
        formulaire.elements['soumettre'].value = 'Veuillez patienter ...';
    }
    return true;
}

// Voir la fiche d'une personne morale à partir d'une personne physique
function voirPersonneMorale() { 
    // On choisit l'élément qui contient l'id de la personne morale
    var select = document.getElementsByName('id_personne_morale'); // On définit l'url où se situe la fiche d'une personne morale
    var id = select[0].options[select[0].selectedIndex].value;
    if (id == 0) { // Si aucune personne morale a été choisie
        alert('Choisissez une personne morale, pour visiter sa fiche.');
    } else { // On redirige vers la personne morale choisie
        document.location.href = '/admin/members/companies/edit/' + select[0].options[select[0].selectedIndex].value;
    }
}

// PHP.JS scripts

function array_map(callback) { 
    // http://kevin.vanzonneveld.net
    // +   original by: Andrea Giammarchi (http://webreflection.blogspot.com)
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // %        note 1: Takes a function as an argument, not a function's name
    // *     example 1: array_map( function(a){return (a * a * a)}, [1, 2, 3, 4, 5] );
    // *     returns 1: [ 1, 8, 27, 64, 125 ]
    var argc = arguments.length,
    argv = arguments;
    var j = argv[1].length,
    i = 0,
    k = 1,
    m = 0;
    var tmp = [],
    tmp_ar = [];
    while (i < j) {
        while (k < argc) {
            tmp[m++] = argv[k++][i];
        }
        m = 0;
        k = 1;
        if (callback) {
            tmp_ar[i++] = callback.apply(null, tmp);
        } else {
            tmp_ar[i++] = tmp;
        }
        tmp = [];
    }
    
    return tmp_ar;
}

function explode(delimiter, string, limit) { 
    // http://kevin.vanzonneveld.net
    // +     original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     improved by: kenneth
    // +     improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +     improved by: d3x
    // +     bugfixed by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // *     example 1: explode(' ', 'Kevin van Zonneveld');
    // *     returns 1: {0: 'Kevin', 1: 'van', 2: 'Zonneveld'}
    // *     example 2: explode('=', 'a=bc=d', 2);
    // *     returns 2: ['a', 'bc=d']
    var emptyArray = {
        0 : ''
    }; // third argument is not required
    if (arguments.length < 2 || typeof arguments[0] == 'undefined' || typeof arguments[1] == 'undefined') {
        return null;
    }
    if (delimiter === '' || delimiter === false || delimiter === null) {
        return false;
    }
    if (typeof delimiter == 'function' || typeof delimiter == 'object' || typeof string == 'function' || typeof string == 'object') {
        return emptyArray;
    }
    if (delimiter === true) {
        delimiter = '1';
    }
    if (!limit) {
        return string.toString().split(delimiter.toString());
    } else { // support for limit argument
        var splitted = string.toString().split(delimiter.toString());
        var partA = splitted.splice(0, limit - 1);
        var partB = splitted.join(delimiter.toString());
        partA.push(partB);
        return partA;
    }
}

function implode(glue, pieces) { 
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Waldo Malqui Silva
    // *     example 1: implode(' ', ['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: 'Kevin van Zonneveld'
    return ((pieces instanceof Array) ? pieces.join(glue) : pieces);
}

function str_replace(search, replace, subject) { 
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Gabriel Paderni
    // +   improved by: Philip Peterson
    // +   improved by: Simon Willison (http://simonwillison.net)
    // +    revised by: Jonas Raoni Soares Silva (http://www.jsfromhell.com)
    // +   bugfixed by: Anton Ongson
    // +      input by: Onno Marsman
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +    tweaked by: Onno Marsman
    // *     example 1: str_replace(' ', '.', 'Kevin van Zonneveld');
    // *     returns 1: 'Kevin.van.Zonneveld'
    // *     example 2: str_replace(['{name}', 'l'], ['hello', 'm'], '{name}, lars');
    // *     returns 2: 'hemmo, mars'
    var f = search,
    r = replace,
    s = subject;
    var ra = r instanceof Array,
    sa = s instanceof Array,
    f = [].concat(f),
    r = [].concat(r),
    i = (s = [].concat(s)).length;
    while (j = 0, i--) {
        if (s[i]) {
            while (s[i] = (s[i] + '').split(f[j]).join(ra ? r[j] || "": r[0]), ++j in f) {};
        }
    };
    
    return sa ? s: s[0];
}

function ucfirst(str) { 
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   bugfixed by: Onno Marsman
    // +   improved by: Brett Zamir
    // *     example 1: ucfirst('kevin van zonneveld');
    // *     returns 1: 'Kevin van zonneveld'
    str += '';
    var f = str.charAt(0).toUpperCase();
    
    return f + str.substr(1);
}
