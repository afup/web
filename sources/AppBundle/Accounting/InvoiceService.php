<?php

namespace AppBundle\Accounting;

use Afup\Site\Comptabilite\Facture;
use Afup\Site\Utils\Configuration;
use Afup\Site\Utils\Mailing;
use League\Flysystem\FilesystemInterface;

class InvoiceService
{
    /** @var Facture */
    private $legacyFacture;
    /** @var FilesystemInterface */
    private $filesystem;
    /** @var string */
    private $raisonSociale;
    /** @var string */
    private $adresse;
    /** @var string */
    private $codePostal;
    /** @var string */
    private $ville;
    /** @var string */
    private $sender;

    public function __construct(
        Configuration $configuration,
        FilesystemInterface $filesystem
    ) {
        global $bdd;
        $this->legacyFacture = new Facture($bdd);
        $this->filesystem = $filesystem;
        $this->raisonSociale = $configuration->obtenir('afup|raison_sociale');
        $this->adresse = $configuration->obtenir('afup|adresse');
        $this->codePostal = $configuration->obtenir('afup|code_postal');
        $this->ville = $configuration->obtenir('afup|ville');
        $this->sender = $configuration->obtenir('mails|email_expediteur');
    }

    /**
     * @param string $reference
     *
     * @return bool
     */
    public function send($reference)
    {
        $personne = $this->legacyFacture->obtenirParNumeroFacture($reference, 'email, nom, prenom');
        $sujet = "Facture AFUP\n";
        $corps = "Bonjour, \n\n"
            . "Veuillez trouver ci-joint la facture correspondant à la participation au forum organisé par l'AFUP.\n"
            . "Nous restons à votre disposition pour toute demande complémentaire.\n\n"
            . "Le bureau\n\n"
            . $this->raisonSociale . "\n"
            . $this->adresse . "\n"
            . $this->codePostal . ' ' . $this->ville . "\n";

        $tmpFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('mail-attachment', true) . '.pdf';
        file_put_contents($tmpFile, $this->get($reference));

        return Mailing::envoyerMail(
            $this->sender,
            [$personne['email'], $personne['nom']],
            $sujet,
            $corps,
            ['file' => [[$tmpFile, 'facture-' . $reference . '.pdf']]]
        );
    }

    /**
     * @param $reference
     *
     * @return string
     */
    public function get($reference)
    {
        $path = 'fact' . $reference . '.pdf';
        if (!$this->filesystem->has($path)) {
            $this->filesystem->write($path, $this->legacyFacture->genererFacture($reference));
        }

        return $this->filesystem->read($path);
    }
}
