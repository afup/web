<?php
namespace Afup\Site;

define('AFUP_ANNUAIREPRO_MEMBRES', 1);

class AFUP_AnnuairePro_Membres
{
    /**
     * @var \Afup\Site\Utils\Base_De_Donnees
     */
    var $_bdd;

    function __construct(& $bdd)
    {
        $this->_bdd = $bdd;
    }

    /**
     *
     */
    function obtenirListe($champs = '*', $ordre = 'DateCreation DESC', $associatif = false)
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '   annuairepro_MembreAnnuaire ';
        $requete .= 'ORDER BY ' . $ordre;

        if ($associatif) {
            return $this->_bdd->obtenirAssociatif($requete);
        } else {
            return $this->_bdd->obtenirTous($requete);
        }
    }

    function obtenirFormesJuridiques()
    {
        $formesJuridiques[0] = '--';
        $formesJuridiques[1] = 'Entreprise Individuelle';
        $formesJuridiques[2] = 'Profession librale';
        $formesJuridiques[3] = 'EURL/SARL';
        $formesJuridiques[4] = 'SA/SAS';
        $formesJuridiques[5] = 'Association';

        return $formesJuridiques;
    }

    function obtenirTaillesEntreprise()
    {
        $taillesEntreprise[0] = '--';
        $taillesEntreprise[1] = 'Une personne';
        $taillesEntreprise[2] = 'Entre 2 et 5 personnes';
        $taillesEntreprise[3] = 'Entre 6 et 10 personnes';
        $taillesEntreprise[4] = 'Plus de 10 personnes';

        return $taillesEntreprise;
    }

    function retoucherSiteWeb($siteWeb, $nom = "")
    {
        $siteWebRetouche = $siteWeb;
        $url = parse_url($siteWeb);
        if (isset ($url['host'])) {
            if (empty ($nom)) {
                $host = $url['host'];
                $nom = preg_replace('/^(www\.)/', '', $host);
            }
            $siteWebRetouche = '<a href="' . $siteWeb . '">' . $nom . '</a>';
        }

        return $siteWebRetouche;
    }

    function retoucherListe($membres)
    {
        $membresAvecRetouche = $membres;
        if (is_array($membres)) {
            $membresAvecRetouche = array();
            foreach ($membres as $membre) {
                $membreAvecRetouche = $membre;
                if (isset ($membre['SiteWeb'])) {
                    $membreAvecRetouche['SiteWeb'] = $this->retoucherSiteWeb($membre['SiteWeb']);
                }

                if (is_numeric($membre['Valide'])) {
                    $membreAvecRetouche['Etat'] = $this->affecterLibelleEtat($membre['Valide'], $membre['ID']);
                }

                $membresAvecRetouche[] = $membreAvecRetouche;
            }
        }

        return $membresAvecRetouche;
    }

    /**
     * @param int $Valide
     * @return string Etat du membre de l'annuaire pro
     */

    function affecterLibelleEtat($Valide, $id)
    {
        switch ($Valide) {
            case 0:
                $Etat = 'Inactif';
                break;

            case 1:
                $Etat = 'Actif';
                break;

            case 9:
                $lien = '<a href="index.php?page=annuairepro_membres&action=moderer&id=' . $id . '">';
                $lien .= 'En attente de mod&eacute;ration';
                $lien .= '</a>';

                $Etat = $lien;
                break;

            default:
                $Etat = 'Pas de statut';
                break;
        }
        return $Etat;
    }

    function obtenir($id, $champs = '*')
    {
        $requete = 'SELECT';
        $requete .= '  ' . $champs . ' ';
        $requete .= 'FROM';
        $requete .= '   annuairepro_MembreAnnuaire ';
        $requete .= 'WHERE ID=' . $id;
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    function ajouter($FormeJuridique, $RaisonSociale, $SIREN, $Email, $SiteWeb, $Telephone, $Fax, $Adresse, $CodePostal, $Ville, $Zone, $id_pays, $NumeroFormateur, $MembreAFUP, $Valide, $DateCreation, $TailleSociete, $Password)
    {
        $requete = 'INSERT INTO ';
        $requete .= '   annuairepro_MembreAnnuaire (FormeJuridique, RaisonSociale, SIREN, Email, SiteWeb, Telephone, Fax, Adresse, CodePostal, Ville, Zone, id_pays, NumeroFormateur, MembreAFUP, Valide, DateCreation, TailleSociete, Password) ';
        $requete .= 'VALUES (';
        $requete .= $this->_bdd->echapper($FormeJuridique) . ',';
        $requete .= $this->_bdd->echapper($RaisonSociale) . ',';
        $requete .= $this->_bdd->echapper($SIREN) . ',';
        $requete .= $this->_bdd->echapper($Email) . ',';
        $requete .= $this->_bdd->echapper($SiteWeb) . ',';
        $requete .= $this->_bdd->echapper($Telephone) . ',';
        $requete .= $this->_bdd->echapper($Fax) . ',';
        $requete .= $this->_bdd->echapper($Adresse) . ',';
        $requete .= $this->_bdd->echapper($CodePostal) . ',';
        $requete .= $this->_bdd->echapper($Ville) . ',';
        $requete .= $this->_bdd->echapper($Zone) . ',';
        $requete .= $this->_bdd->echapper($id_pays) . ',';
        $requete .= $this->_bdd->echapper($NumeroFormateur) . ',';
        $requete .= $this->_bdd->echapper($MembreAFUP) . ',';
        $requete .= $this->_bdd->echapper($Valide) . ',';
        $requete .= $this->_bdd->echapper($DateCreation) . ',';
        $requete .= $this->_bdd->echapper($TailleSociete) . ',';
        $requete .= $this->_bdd->echapper($Password) . ')';

        return $this->_bdd->executer($requete);
    }

    /**
     * @param string $Valide 0 = Inactif,  1 = Actif, 9 = A modérer
     * @return bool
     */
    function modifier($id, $FormeJuridique, $RaisonSociale, $SIREN, $Email, $SiteWeb, $Telephone, $Fax, $Adresse, $CodePostal, $Ville, $Zone, $id_pays, $NumeroFormateur, $MembreAFUP, $Valide, $DateCreation, $TailleSociete, $Password)
    {
        $requete = 'UPDATE ';
        $requete .= '   annuairepro_MembreAnnuaire ';
        $requete .= 'SET';
        $requete .= '  FormeJuridique=' . $this->_bdd->echapper($FormeJuridique) . ',';
        $requete .= '  RaisonSociale=' . $this->_bdd->echapper($RaisonSociale) . ',';
        $requete .= '  SIREN=' . $this->_bdd->echapper($SIREN) . ',';
        $requete .= '  Email=' . $this->_bdd->echapper($Email) . ',';
        $requete .= '  SiteWeb=' . $this->_bdd->echapper($SiteWeb) . ',';
        $requete .= '  Telephone=' . $this->_bdd->echapper($Telephone) . ',';
        $requete .= '  Fax=' . $this->_bdd->echapper($Fax) . ',';
        $requete .= '  Adresse=' . $this->_bdd->echapper($Adresse) . ',';
        $requete .= '  CodePostal=' . $this->_bdd->echapper($CodePostal) . ',';
        $requete .= '  Ville=' . $this->_bdd->echapper($Ville) . ',';
        $requete .= '  Zone=' . $this->_bdd->echapper($Zone) . ',';
        $requete .= '  id_pays=' . $this->_bdd->echapper($id_pays) . ',';
        $requete .= '  NumeroFormateur=' . $this->_bdd->echapper($NumeroFormateur) . ',';
        $requete .= '  MembreAFUP=' . $this->_bdd->echapper($MembreAFUP) . ',';
        $requete .= '  Valide=' . $this->_bdd->echapper($Valide) . ',';
        $requete .= '  DateCreation=' . $this->_bdd->echapper($DateCreation) . ',';
        $requete .= '  TailleSociete=' . $this->_bdd->echapper($TailleSociete) . ',';
        $requete .= '  Password=' . $this->_bdd->echapper($Password);
        $requete .= 'WHERE';
        $requete .= '  ID=' . $id;

        return $this->_bdd->executer($requete);
    }

    function supprimer($id)
    {
        $requete = 'DELETE FROM  annuairepro_MembreAnnuaire WHERE ID=' . $id;
        return $this->_bdd->executer($requete);
    }

    function nettoyer()
    {
        $requete = 'DELETE FROM annuairepro_MembreAnnuaire ';
        $requete .= 'WHERE RaisonSociale = ""               ';
        $requete .= 'AND Valide != 1';
        return $this->_bdd->executer($requete);
    }

    function validerInscription($id)
    {
        $requete = 'UPDATE  annuairepro_MembreAnnuaire SET ' .
            'Valide = 1 ' .
            'WHERE ID=' . $id;

        print($requete);

        return $this->_bdd->executer($requete);
    }

    function VerifierSIREN($SIREN)
    {
        $verification = 'KO';

        $SIREN = preg_replace('/([\D])/', '', $SIREN);
        $SIREN = substr($SIREN, 0, 9);
        $init = 'http://www.societe.com/cgi-bin/recherche?rncs=' . $SIREN . '&image.x=46&image.y=12';

        if (function_exists('curl_init')) {
            $curl_check = curl_init($init);
            curl_setopt($curl_check, CURLOPT_RETURNTRANSFER, TRUE);
            if (is_array(curl_getinfo($curl_check))) {
                $reponse = curl_exec($curl_check);
                if (!preg_match('/Pas de réponse pour ce RNCS/', $reponse)) {
                    $verification = 'OK';
                }
            }
            curl_close($curl_check);
        } else {
            if ($this->siren($SIREN)) {
                $verification = 'OK';
            }
        }

        return $verification;
    }

    /**
     * Validate a french SIREN number
     * see http://xml.insee.fr/schema/siret.html
     *
     * @param  string $siren number or an array containaing the 'number'=>1234
     * @return bool           true if number is valid, otherwise false
     * @author Damien Seguy <dams@nexen.net>
     */
    function siren($siren)
    {
        $siren = str_replace(' ', '', $siren);
        if (!preg_match("/^(\d)(\d)(\d)(\d)(\d)(\d)(\d)(\d)(\d)$/", $siren, $match)) {
            return false;
        }
        $match[2] *= 2;
        $match[4] *= 2;
        $match[6] *= 2;
        $match[8] *= 2;
        $sum = 0;

        for ($i = 1; $i < count($match); $i++) {
            if ($match[$i] > 9) {
                $a = (int)substr($match[$i], 0, 1);
                $b = (int)substr($match[$i], 1, 1);
                $match[$i] = $a + $b;
            }
            $sum += $match[$i];
        }
        return (($sum % 10) == 0);
    }
}

?>