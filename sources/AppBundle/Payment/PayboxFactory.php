<?php


namespace AppBundle\Payment;


use Afup\Site\Utils\Configuration;

class PayboxFactory
{
    /**
     * @var Configuration
     */
    private $conf;

    /**
     * @param string $facture Facture id
     * @param float $montant Amount to pay
     * @param string $email Email of the company
     * @return string html with payment button
     */
    public function createPayboxForSubscription($facture, $montant, $email)
    {
        require_once 'paybox/payboxv2.inc';

        $paybox = new \paybox();
        $paybox->set_langue('FRA'); // Langue de l'interface PayBox
        $paybox->set_site($this->getConf()->obtenir('paybox|site'));
        $paybox->set_rang($this->getConf()->obtenir('paybox|rang'));
        $paybox->set_identifiant('83166771'); ///  @todo this should not be here

        $paybox->set_total($montant * 100); // Total de la commande, en centimes d'euros
        $paybox->set_cmd($facture); // Référence de la commande
        $paybox->set_porteur($email); // Email du client final (Le porteur de la carte)

        $paybox->set_repondreA('http://' . $_SERVER['HTTP_HOST'] . '/pages/administration/paybox_retour.php');
        $paybox->set_effectue('http://' . $_SERVER['HTTP_HOST'] . '/pages/administration/paybox_effectue.php');
        $paybox->set_refuse('http://' . $_SERVER['HTTP_HOST'] . '/pages/administration/paybox_refuse.php');
        $paybox->set_annule('http://' . $_SERVER['HTTP_HOST'] . '/pages/administration/paybox_annule.php');
        $paybox->set_erreur('http://' . $_SERVER['HTTP_HOST'] . '/pages/administration/paybox_erreur.php');

        $paybox->set_wait(50000); // Délai d'attente avant la redirection
        $paybox->set_boutpi('R&eacute;gler par carte'); // Texte du bouton
        $paybox->set_bkgd('#FAEBD7'); // Fond de page
        $paybox->set_output('B'); // On veut gerer l'affichage dans la page intermediaire

        if (preg_match('#<CENTER>.*</b>(.*)</CENTER>#is', $paybox->paiement(), $r)) {
            return $r[1];
        } else {
            throw new \RuntimeException('Could not create the payment');
        }
    }

    /**
     * @return Configuration
     */
    private function getConf()
    {
        if ($this->conf === null) {
            if (isset($GLOBALS['AFUP_CONF']) === false) {
                throw new \RuntimeException('Configuration undefined');
            }
            $this->conf = $GLOBALS['AFUP_CONF'];
        }

        return $this->conf;
    }
}
