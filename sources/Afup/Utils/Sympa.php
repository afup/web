<?php

declare(strict_types=1);

namespace Afup\Site\Utils;

class Sympa
{
    public function __construct(
        private readonly Base_De_Donnees $_bdd,
        private $_configUrl,
        private $configHost,
    ) {
    }

    public function getAllMailingList()
    {
        $curl = curl_init($this->_configUrl);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Host: ' . $this->configHost]);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        return unserialize(curl_exec($curl));
    }

    public function getAllUsers()
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  user_table ';
        return $this->_bdd->obtenirAssociatif($requete);
    }

    public function subscribe($email, $list, $nom = null)
    {
        $requete = "INSERT INTO subscriber_table (list_subscriber, user_subscriber, custom_attribute_subscriber,
                                                  robot_subscriber, date_subscriber, update_subscriber,
                                                  visibility_subscriber, reception_subscriber, topics_subscriber,
                                                  bounce_subscriber, bounce_score_subscriber, bounce_address_subscriber,
                                                  comment_subscriber, subscribed_subscriber, included_subscriber,
                                                  include_sources_subscriber)
                    VALUES (" . $this->_bdd->echapper($list) . ", " . $this->_bdd->echapper($email) . ", '<?xml version=\"1.0\" encoding=\"UTF-8\" ?><custom_attributes></custom_attributes>',
                            'lists.afup.org', '" . date('Y-m-d H:i:s') . "', '" . date('Y-m-d H:i:s') . "',
                            'noconceal', 'mail', NULL,
                            NULL, NULL, NULL,
                            " . $this->_bdd->echapper($nom) . ", 1, 0,
                            NULL)";
        return $resultat = $this->_bdd->executer($requete);
    }

    public function unsubscribe($email, $list)
    {
        $requete = "DELETE FROM sympa.subscriber_table
                    WHERE subscriber_table.list_subscriber = " . $this->_bdd->echapper($list) . "
                    AND subscriber_table.user_subscriber = " . $this->_bdd->echapper($email) . "
                    AND subscriber_table.robot_subscriber = 'lists.afup.org'";
        return $resultat = $this->_bdd->executer($requete);
    }

    public function getMailingListUser($email)
    {
        $requete = 'SELECT';
        $requete .= '  list_subscriber ';
        $requete .= 'FROM';
        $requete .= '  subscriber_table ';
        $requete .= 'WHERE';
        $requete .= '  user_subscriber = ' . $this->_bdd->echapper($email) . ' ';
        return $this->_bdd->obtenirColonne($requete);
    }

    public function getInscritsMailingList($ml)
    {
        $requete = 'SELECT';
        $requete .= '  user_subscriber ';
        $requete .= 'FROM';
        $requete .= '  subscriber_table ';
        $requete .= 'WHERE';
        $requete .= '  list_subscriber = ' . $this->_bdd->echapper($ml) . ' ';
        return $this->_bdd->obtenirColonne($requete);
    }

    public function getUser($email)
    {
        $requete = 'SELECT';
        $requete .= '  * ';
        $requete .= 'FROM';
        $requete .= '  user_table ';
        $requete .= 'WHERE';
        $requete .= '  email_user = ' . $this->_bdd->echapper($email) . ' ';
        return $this->_bdd->obtenirEnregistrement($requete);
    }

    public function createUser($email, $nom)
    {
        $requete = 'INSERT INTO ';
        $requete .= '  user_table (email_user, gecos_user, lang_user) ';
        $requete .= 'VALUES (';
        $requete .= $this->_bdd->echapper($email) . ',';
        $requete .= $this->_bdd->echapper($nom) . ',';
        $requete .= '\'fr\')';
        return $this->_bdd->executer($requete);
    }

    public function deleteUser($email)
    {
        $requete = 'DELETE FROM ';
        $requete .= '  user_table ';
        $requete .= 'WHERE ';
        $requete .= '  email_user = ' . $this->_bdd->echapper($email) . ' ';
        return $this->_bdd->executer($requete);
    }
}
