<?php

/**
 * Classe principale de gestion de l'annuaire. (singleton)
 *
 * @author Guillaume Ponçon <guillaume.poncon@wanadoo.fr>
 * @version 1.0
 * @copyright 2006 Association Française des Utilisateurs de PHP
 * @since 1.0 - Thu Jul 13 14:18:58 CEST 2006
 * @package afup
 */
class Afup_Directory
{

    /**
     * PDO Access
     *
     * @var PDO
     */
    private $pdo = null;

    /**
     * Instance de la classe (singleton).
     *
     * @var Afup_Directory
     */
    private static $instance = null;

    /**
     * Empêche l'instanciation manuelle du singleton sauf si appel interne.
     *
     * @return Afup_Directory
     */
    private function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Charge le contexte de la classe.
     *
     * @param string $host
     * @param string $user
     * @param string $pass
     * @param string $db
     * @return Afup_Directory
     */
    public static function getInstance(PDO $pdo)
    {
        if (self::$instance === null) {
            self::$instance = new Afup_Directory($pdo);
        }
        return self::$instance;
    }

    /**
     * Execute une requête SQL (mysql).
     *
     * @todo implémenter le template.
     * @todo eviter de dupliquer les objets PDO, mais maintenir le renvoi d'une collection ouverte sur la BD
     * @param string $sql
     * @param string $template
     * @throws PDOException
     * @throws Afup_Directory_Exception
     * @return integer|PDOStatement insert or select
     */
    private function executeSql($sql, $template = null, $values = null)
    {
        if ($values) {
            $stmt = $this->pdo->prepare($sql);
            if ($stmt) {
                if (isset($values[0]) && is_array($values[0])) {
                    foreach ($values as $value) {
                        $stmt->execute($value);
                    }
                } else {
                    $stmt->execute($values);
                }
            }
        } else {
            $stmt = $this->pdo->query($sql, PDO::FETCH_ASSOC);
        }
        $queryType = strtolower(substr($sql, 0, 6));
        switch ($queryType) {
            case 'select' :
                return $stmt;
                // break;
            case 'insert' :
            case 'update' :
            case 'delete' :
                unset($stmt);
                return $this->pdo->lastInsertId();
                // break;
            default:
                throw new Afup_Directory_Exception("This request do not return values.");
                break;
        }
    }

    /**
     * Renvoit un tableau d'entreprises pour affichage.
     *
     * @param integer $page numéro de page
     * @param integer $nbr nombre d'entreprises par page
     * @param array $params paramêtres de la requête (tri, filtre, etc.)
     * @param integer $getCount renvoit le nombre total de résultats
     * @return PDOStatement
     */
    public function getCompanies($page = 0, $nbr = 10, $params = null, $getCount = false)
    {
        $sql  = 'SELECT DISTINCT ';
        $sql .= $getCount ? 'count(DISTINCT ID) AS count' : 'ID, RaisonSociale, SiteWeb, Adresse, CodePostal, Ville, FormeJuridique, Telephone, Fax';
        $sql .= ' FROM annuairepro_MembreAnnuaire, annuairepro_ActiviteMembre WHERE ID=Membre ';
        if (is_array($params['criteria'])) {
            foreach ($params['criteria'] as $key => $value) {
                $sql .= ' AND ' . $key . '=\'' . $value . '\' ';
            }
        }
        $sql .= $getCount ? '' : ' ORDER BY RaisonSociale ASC LIMIT ' . ($page * $nbr) . ', ' . $nbr;
        return $this->executeSql($sql);
    }

    /**
     * Renvoit le détail d'une entreprise.
     *
     * @param integer $id
     * @return PDOStatement
     */
    public function getCompany($id)
    {
        if (!is_numeric($id)) {
            return false;
        }
        $sql  = 'SELECT * FROM annuairepro_MembreAnnuaire WHERE ';
        $sql .= 'annuairepro_MembreAnnuaire.ID = \'' . (int) $id . '\' ';
        if (isset($params['criteria']) && is_array($params['criteria'])) {
            foreach ($params['criteria'] as $key => $value) {
                $sql .= ' AND ' . $key . '=\'' . $value . '\' ';
            }
        }
        return $this->executeSql($sql);
    }

    /**
     * Renvoit le détail d'une entreprise dans un objet Afup_Directory_Member
     *
     * @throws Afup_Directory_Exception
     * @return Afup_Directory_Member
     */
    public function getDirectoryMember($id)
    {
        $detail = $this->getCompany($id);
        $row = $detail->fetch();
        if (!$row) {
            throw new Afup_Directory_Exception('Directory member ' . $id . ' not found.');
        }
        return new Afup_Directory_Member($row);
    }

    /**
     * Renvoit le nombre d'entreprises concerné par les paramêtres mentionnés.
     *
     * @param array $params
     * @return integer
     */
    public function countCompanies($params = null)
    {
        $result = $this->getCompanies(null, null, $params, true);
        $row = $result->fetch();
        unset($result);
        return (int) $row['count'];
    }

    /**
     * Met à jour une entreprise.
     *
     * @param array $companyTable tableau récupéré du formulaire
     * @param boolean $add ajout d'une entreprise si true
     * @return array tableau d'erreurs
     * @todo implémenter la modification
     */
    public function updateCompany($v, $add = false)
    {
        if ($add === true) {
            $sql = 'INSERT INTO `annuairepro_MembreAnnuaire` ';
            $sql .= '( `ID` , `FormeJuridique` , `RaisonSociale` , ';
            $sql .= '`SIREN` , `Email` , `SiteWeb` , `Telephone` , `Fax` ';
            $sql .= ', `Adresse` , `CodePostal` , `Ville` , `Zone` , ';
            $sql .= '`NumeroFormateur` , `MembreAFUP` , `Valide` , ';
            $sql .= '`DateCreation` , `TailleSociete` , `Password` )';
            $sql .= ' VALUES (\'\', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 9, NOW() , ?, ?);';
            $values = array($v['FormeJuridique'], $v['RaisonSociale'], $v['SIREN'], $v['Email'], $v['SiteWeb'], $v['Telephone'], $v['Fax'], $v['Adresse'], $v['CodePostal'], $v['Ville'], $v['Zone'], $v['NumFormateur'], $v['TailleSociete'], $v['Password']);
            $member = $this->executeSql($sql, null, $values);
            if ($member) {
                $sql = 'INSERT INTO annuairepro_ActiviteMembre (Membre, Activite, EstPrincipale) VALUES (?, ?, ?);';
                $values = array();
                $values[] = array($member, $v['ActivitePrincipale'], 'True');
                if (isset($v['ActivitesSecondaires']) && count($v['ActivitesSecondaires'])) {
                    foreach ($v['ActivitesSecondaires'] as $activity) {
                        if ($activity == $v['ActivitePrincipale']) {
                            continue;
                        }
                        $values[] = array($member, $activity, 'False');
                    }
                }
                try {
                    $this->executeSql($sql, null, $values);
                    return $member;
                } catch (PDOException $e) {
                    $this->removeCompany($member);
                    throw $e;
                }
                return false;
            }
            return false;
        }
        return false;
    }

    /**
     * Ajout d'une entreprise (fait appel à updateCompany)
     *
     * @param array $values
     * @return array tableau d'erreurs
     */
    public function addCompany($values)
    {
        return $this->updateCompany($values, true);
    }

    /**
     * Retrait définitif d'une entreprise (membre)
     *
     * @param integer $companyId
     * @return boolean
     */
    public function removeCompany($companyId)
    {
        $this->executeSql("DELETE FROM annuairepro_MembreAnnuaire WHERE ID=?", null, array($companyId));
        $this->executeSql("DELETE FROM annuairepro_ActiviteMembre WHERE Membre=?", null, array($companyId));
    }

    /**
     * Suppression des liens activités-membres orphelins de membre
     *
     * @return integer
     */
    private function cleanMemberActivity()
    {
        $sql = "DELETE FROM annuairepro_ActiviteMembre WHERE Membre NOT IN (SELECT ID FROM annuairepro_MembreAnnuaire)";
        return $this->executeSql($sql);
    }

    /**
     * Met à jour le statut de visibilité d'une entreprise.
     *
     * @param integer $companyId
     * @param boolean $status
     * @return boolean true si tout s'est bien passé
     * @todo à implémenter
     */
    private function _updateCompanyStatus($companyId, $status)
    {

    }

    /**
     * Met le statut d'une entreprise à 1. (visible)
     *
     * @param integer $companyId
     * @return boolean
     * @todo à implémenter
     */
    public function validateCompany($companyId)
    {
        return $this->_updateCompany($companyId, true);
    }

    /**
     * Met le statut d'une entreprise à 0. (invisible)
     *
     * @param integer $companyId
     * @return boolean
     * @todo à implémenter
     */
    public function unvalidateCompany($companyId)
    {
        return $this->_updateCompany($companyId, false);
    }
}