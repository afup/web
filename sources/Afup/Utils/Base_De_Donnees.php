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
     * @return void
     */
    public function __construct($host, $database, $user, $password, $port = null)
    {
        $this->config = [
            'host' => $host,
            'database' => $database,
            'user' => $user,
            'password' => $password,
            'port' => $port,
        ];
    }

    public function getDbLink()
    {
        if ($this->link === null) {
            $this->link = mysqli_connect($this->config['host'], $this->config['user'], $this->config['password'], null, (int) $this->config['port']) or die('Connexion à la base de données impossible');
            mysqli_set_charset($this->link, "utf8mb4");
            $this->selectionnerBase($this->config['database']);
        }
        return $this->link;
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
