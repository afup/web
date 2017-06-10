<?php


namespace AppBundle\Payment;

use Afup\Site\Utils\Configuration;
use AppBundle\Event\Model\Event;
use AppBundle\Event\Model\Invoice;
use Symfony\Component\Routing\RouterInterface;

class PayboxFactory
{
    /**
     * @var Configuration
     */
    private $conf;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    /**
     * @param string $facture Facture id
     * @param float $montant Amount to pay
     * @param string $email Email of the company
     * @return string html with payment button
     */
    public function createPayboxForSubscription($facture, $montant, $email)
    {
        $paybox = $this->getPaybox();

        $paybox->set_total($montant * 100); // Total de la commande, en centimes d'euros
        $paybox->set_cmd($facture); // Référence de la commande
        $paybox->set_porteur($email); // Email du client final (Le porteur de la carte)

        $paybox->set_repondreA('http://' . $_SERVER['HTTP_HOST'] . '/pages/administration/paybox_retour.php');
        $paybox->set_effectue('http://' . $_SERVER['HTTP_HOST'] . '/pages/administration/paybox_effectue.php');
        $paybox->set_refuse('http://' . $_SERVER['HTTP_HOST'] . '/pages/administration/paybox_refuse.php');
        $paybox->set_annule('http://' . $_SERVER['HTTP_HOST'] . '/pages/administration/paybox_annule.php');
        $paybox->set_erreur('http://' . $_SERVER['HTTP_HOST'] . '/pages/administration/paybox_erreur.php');

        if (preg_match('#<CENTER>.*</b>(.*)</CENTER>#is', $paybox->paiement(), $r)) {
            return $r[1];
        } else {
            throw new \RuntimeException('Could not create the payment');
        }
    }

    public function createPayboxForTicket(Invoice $invoice, Event $event)
    {
        $paybox = $this->getPaybox();

        $paybox->set_total($invoice->getAmount() * 100); // Total de la commande, en centimes d'euros
        $paybox->set_cmd($invoice->getReference()); // Référence de la commande
        $paybox->set_porteur($invoice->getEmail()); // Email du client final (Le porteur de la carte)

        $paybox->set_repondreA($this->router->generate('ticket_paybox_callback', ['eventSlug' => $event->getPath()]));
        $returnUrl = $this->router->generate('ticket_paybox_callback', ['eventSlug' => $event->getPath()]);

        $paybox->set_effectue($returnUrl);
        $paybox->set_refuse($returnUrl);
        $paybox->set_annule($returnUrl);
        $paybox->set_erreur($returnUrl);

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

    /**
     * @return \paybox
     */
    private function getPaybox()
    {
        require_once 'paybox/payboxv2.inc';

        $paybox = new \paybox();
        $paybox->set_langue('FRA'); // Langue de l'interface PayBox
        $paybox->set_site($this->getConf()->obtenir('paybox|site'));
        $paybox->set_rang($this->getConf()->obtenir('paybox|rang'));
        $paybox->set_identifiant('83166771'); ///  @todo this should not be here

        $paybox->set_wait(50000); // Délai d'attente avant la redirection
        $paybox->set_boutpi('R&eacute;gler par carte'); // Texte du bouton
        $paybox->set_bkgd('#FAEBD7'); // Fond de page
        $paybox->set_output('B');
        return $paybox; // On veut gerer l'affichage dans la page intermediaire
    }
}
