<?php

declare(strict_types=1);

namespace Afup\Site\Utils;

/**
 * Classe d'abstraction pour la base de données
 */
class Base_De_Donnees
{
    /**
     * Lien de la connection vers le serveur
     * @var     \mysqli
     */
    private $link;

    private array $config;

    /**
     * Contructeur. Etablit une connexion au serveur et sélectionne la base de données indiquée
     *
     * @param string $host Adresse du serveur
     * @param string $database Nom de la base
     * @param string $user Nom de l'utilisateur
     * @param string $password Mot de passe
     * @param string $timezone Timezone de la base de données
     * @return void
     */
    public function __construct($host, $database, $user, $password, $port = null, $timezone = null)
    {
        $this->config = [
            'host' => $host,
            'database' => $database,
            'user' => $user,
            'password' => $password,
            'port' => (int) $port,
            'timezone' => $timezone,
        ];
    }

    public function getDbLink()
    {
        if ($this->link === null) {
            $this->link = mysqli_connect($this->config['host'], $this->config['user'], $this->config['password'], null, $this->config['port']) or die('Connexion à la base de données impossible');
            if ($this->config['timezone']) {
                mysqli_query($this->link, "SET time_zone = '" . $this->config['timezone'] . "'");
            }
            mysqli_set_charset($this->link, "utf8mb4");
            $this->selectionnerBase($this->config['database']);
        }
        return $this->link;
    }

    /**
     * Scinde un ensemble de requètes SQL en un tableau regroupant ces requètes
     *
     * ATTENTION    : Fonction importée depuis phpMyAdmin.
     * Nom original : PMA_splitSqlFile().
     *
     * @param      string      $sql Requetes SQL à scinder
     * @return     array       Tableau contenant les requètes SQL
     */
    public function _scinderRequetesSql($sql): array
    {
        // do not trim, see bug #1030644
        //$sql          = trim($sql);
        $sql = rtrim($sql, "\n\r");
        $sql_len = strlen($sql);
        $char = '';
        $string_start = '';
        $in_string = false;
        $nothing = true;
        $time0 = time();
        $ret = [];

        for ($i = 0; $i < $sql_len; ++$i) {
            $char = $sql[$i];

            // We are in a string, check for not escaped end of strings except for
            // backquotes that can't be escaped
            if ($in_string) {
                for (; ;) {
                    $i = strpos($sql, $string_start, $i);
                    // No end of string found -> add the current substring to the
                    // returned array
                    if (!$i) {
                        $ret[] = ['query' => $sql, 'empty' => $nothing];
                        return $ret;
                    } elseif ($string_start === '`' || $sql[$i - 1] !== '\\') {
                        $string_start = '';
                        $in_string = false;
                        break;
                    } else {
                        // ... first checks for escaped backslashes
                        $j = 2;
                        $escaped_backslash = false;
                        while ($i - $j > 0 && $sql[$i - $j] === '\\') {
                            $escaped_backslash = !$escaped_backslash;
                            $j++;
                        }
                        // ... if escaped backslashes: it's really the end of the
                        // string -> exit the loop
                        if ($escaped_backslash) {
                            $string_start = '';
                            $in_string = false;
                            break;
                        } // ... else loop
                        else {
                            $i++;
                        }
                    } // end if...elseif...else
                }
                // end for
            } elseif (($char === '-' && $sql_len > $i + 2 && $sql[$i + 1] === '-' && $sql[$i + 2] <= ' ') || $char === '#' || ($char === '/' && $sql_len > $i + 1 && $sql[$i + 1] === '*')) {
                $i = strpos($sql, $char === '/' ? '*/' : "\n", $i);
                // didn't we hit end of string?
                if ($i === false) {
                    break;
                }
                if ($char === '/') {
                    $i++;
                }
            } elseif ($char === ';') {
                // if delimiter found, add the parsed part to the returned array
                $ret[] = ['query' => substr($sql, 0, $i), 'empty' => $nothing];
                $nothing = true;
                $sql = ltrim(substr($sql, min($i + 1, $sql_len)));
                $sql_len = strlen($sql);
                if ($sql_len !== 0) {
                    $i = -1;
                } else {
                    // The submited statement(s) end(s) here
                    return $ret;
                }
            } elseif (($char === '"') || ($char === '\'') || ($char === '`')) {
                $in_string = true;
                $nothing = false;
                $string_start = $char;
            } elseif ($nothing) {
                $nothing = false;
            }

            // loic1: send a fake header each 30 sec. to bypass browser timeout
            $time1 = time();
            if ($time1 >= $time0 + 30) {
                $time0 = $time1;
                header('X-pmaPing: Pong');
            } // end if
        } // end for

        // add any rest to the returned array
        if ($sql !== '' && $sql !== '0' && preg_match('@[^[:space:]]+@', $sql)) {
            $ret[] = ['query' => $sql, 'empty' => $nothing];
        }

        return $ret;
    }


    /**
     * Sélectionne la base de données indiquée
     *
     * @param string $nom Nom de la base
     */
    public function selectionnerBase($nom): bool
    {
        return mysqli_select_db($this->getDbLink(), $nom);
    }

    /**
     * Prépare une valeur qui va être incorporée dans une requête SQL
     *
     * @param mixed $valeur Valeur à traiter
     * @return string   La valeur traitée
     */
    public function echapper($valeur): string
    {
        if (is_string($valeur)) {
            $valeur = "'" . mysqli_real_escape_string($this->getDbLink(), $valeur) . "'";
        } elseif (is_null($valeur)) {
            $valeur = 'NULL';
        }
        return (string) $valeur;
    }

    /**
     *
     * @param array $date
     * @param boolean $timestamp
     * @return int|string
     */
    public function echapperSqlDateFromQuickForm($date, $timestamp = false)
    {
        $dateChaine = $date['Y'] . '-' . $date['M'] . '-' . $date['d'];
        if (isset($date['H']) && isset($date['i'])) {
            $dateChaine .= ' ' . $date['H'] . ':' . $date['i'];
            if (isset($date['s'])) {
                $dateChaine .= ':' . $date['s'];
            }
        }
        if ($timestamp) {
            return strtotime($dateChaine);
        } else {
            return $this->echapper($dateChaine);
        }
    }

    /**
     * Retrieve the last error message
     * @return string
     */
    public function getLastErrorMessage(): ?string
    {
        return mysqli_error($this->getDbLink());
    }

    /**
     * Exécute une requête SQL
     *
     * @param string $requete Requête à exécuter
     * @return bool
     */
    public function executer($requete)
    {
        $result = mysqli_query($this->getDbLink(), $requete);
        if (!$result) {
            throw new \RuntimeException(mysqli_error($this->getDbLink()));
        }

        return $result;
    }


    /**
     * Exécute les requêtes SQL d'un fichier
     *
     * @param string $fichier Nom du fichier avec les requêtes à exécuter
     */
    public function executerFichier($fichier): bool
    {
        if (!file_exists($fichier)) {
            return false;
        }

        $requetes = $this->_scinderRequetesSql(file_get_contents($fichier));
        foreach ($requetes as $requete) {
            if (!$this->executer($requete['query'])) {
                return false;
            }
        }

        return true;
    }


    /**
     * Exécute une requête SQL et retourne le premier champ du premier enregistrement
     *
     * @param string $requete Requête à exécuter
     * @return mixed    Le premier champ du premier enregistrement ou false si la requête échoue
     */
    public function obtenirUn($requete)
    {
        $enregistrement = $this->obtenirEnregistrement($requete, MYSQLI_NUM);
        if ($enregistrement === false) {
            return false;
        } else {
            return $enregistrement[0];
        }
    }

    /**
     * Exécute une requête SQL et retourne le premier enregistrement correspondant
     *
     * @param string $requete Requête à exécuter
     * @param int $type Type de résultat souhaité. Les valeurs possibles sont MYSQLI_ASSOC, MYSQLI_NUM, MYSQLI_BOTH.
     *                              Elles permettent respectivement de récupérer les valeurs sous forme d'un tableau associatif, indexé ou les deux.
     *                              La valeur par défaut est MYSQLI_ASSOC.
     * @return mixed    L'enregistrement correspondant dans un tableau ou false si la requête échoue
     */
    public function obtenirEnregistrement($requete, $type = MYSQLI_ASSOC)
    {
        $ressource = mysqli_query($this->getDbLink(), $requete);
        if ($ressource === false) {
            return false;
        }
        $enregistrement = mysqli_fetch_array($ressource, $type);
        mysqli_free_result($ressource);

        if ($enregistrement === null) {
            return false;
        } else {
            return $enregistrement;
        }
    }

    /**
     * Exécute une requête SQL et retourne les enregistrements correspondant
     *
     * @param string $requete Requête à exécuter
     * @param int $type Type de résultat souhaité. Les valeurs possibles sont MYSQLI_ASSOC, MYSQLI_NUM, MYSQLI_BOTH.
     *                              Elles permettent respectivement de récupérer les valeurs sous forme d'un tableau associatif, indexé ou les deux.
     *                              La valeur par défaut est MYSQLI_ASSOC.
     * @return mixed    Les enregistrements correspondant dans un tableau ou false si la requête échoue
     */
    public function obtenirTous($requete, $type = MYSQLI_ASSOC)
    {
        $ressource = mysqli_query($this->getDbLink(), $requete);
        if ($ressource === false) {
            return false;
        }

        $resultat = [];
        while ($enregistrement = mysqli_fetch_array($ressource, $type)) {
            $resultat[] = $enregistrement;
        }
        mysqli_free_result($ressource);

        return $resultat;
    }

    /**
     * Exécute une requête SQL et retourne les enregistrements correspondant
     *
     * @param string $requete Requête à exécuter
     * @return mixed    Les enregistrements correspondant dans un tableau ou false si la requête échoue
     */
    public function obtenirColonne($requete)
    {
        $ressource = mysqli_query($this->getDbLink(), $requete);
        if ($ressource === false) {
            return false;
        }

        $resultat = [];
        while ($enregistrement = mysqli_fetch_array($ressource)) {
            $resultat[] = $enregistrement[0];
        }
        mysqli_free_result($ressource);

        return $resultat;
    }

    /**
     * Exécute une requête SQL et retourne les enregistrements correspondant dans un tableau associatif dont le premier champ est la clé
     *
     * @param string $requete Requête à exécuter
     * @return mixed    Les enregistrements correspondant dans un tableau associatif ou false si la requête échoue
     */
    public function obtenirAssociatif($requete)
    {
        $ressource = mysqli_query($this->getDbLink(), $requete);
        $nombre_champs = mysqli_num_fields($ressource);
        if ($ressource === false || $nombre_champs < 2) {
            return false;
        }

        // $i      = 0;
        // $champs = array();
        // while ($i < $nombre_champs) {
        //     $champs[$i] = MYSQLI_field_name($ressource, $i);
        //     $i++;
        // }
        $champs = mysqli_fetch_fields($ressource);

        $resultat = [];
        if ($nombre_champs == 2) {
            while ($enregistrement = mysqli_fetch_array($ressource, MYSQLI_NUM)) {
                $resultat[$enregistrement[0]] = $enregistrement[1];
            }
        } else {
            while ($enregistrement = mysqli_fetch_array($ressource, MYSQLI_ASSOC)) {
                $resultat[$enregistrement[$champs[0]->name]] = array_slice($enregistrement, 1);
            }
        }
        mysqli_free_result($ressource);

        return $resultat;
    }

    public function obtenirDernierId()
    {
        return mysqli_insert_id($this->getDbLink());
    }
}
