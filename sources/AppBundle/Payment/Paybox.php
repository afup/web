<?php

namespace AppBundle\Payment;

class Paybox
{
    const DEVISE_EURO = 978;

    /**
     * cf http://www.paybox.com/espace-integrateur-documentation/dictionnaire-des-donnees/paybox-system/
     */
    const RETOUR = 'total:M;cmd:R;autorisation:A;transaction:T;status:E';
    const SOURCE = 'HTML';
    const HASH = 'SHA512';
    const TYPE_PAIEMENT = 'CARTE';
    const TYPECARTE = 'CB';

    private $domainServer;
    private $secretKey;
    private $site;
    private $rang;
    private $identifiant;

    private $total = 0;
    private $cmd = null;
    private $porteur = null;
    private $urlRetourEffectue = null;
    private $urlRetourRefuse = null;
    private $urlRetourErreur = null;
    private $urlRetourAnnule = null;
    private $urlRepondreA = null;

    public function __construct($domainServer, $secretKey, $site, $rang, $identifiant)
    {
        // la configuration des domainServer est visible ici http://www.paybox.com/espace-integrateur-documentation/la-solution-paybox-system/urls-dappels-et-adresses-ip/
        $this->domainServer = $domainServer;
        $this->secretKey = $secretKey;
        $this->site = $site;
        $this->rang = $rang;
        $this->identifiant = $identifiant;
    }

    public function generate(\DateTimeInterface $now)
    {
        // On récupère la date au format ISO-8601
        $dateTime = $now->format('c');

        $inputs = [
            'PBX_SITE' => $this->site,
            'PBX_RANG' => $this->rang,
            'PBX_IDENTIFIANT' => $this->identifiant,
            'PBX_TOTAL' => $this->total,
            'PBX_DEVISE' => self::DEVISE_EURO,
            'PBX_LANGUE' => 'FRA',
            'PBX_CMD' => $this->cmd,
            'PBX_PORTEUR' => $this->porteur,
            'PBX_ANNULE' => $this->urlRetourAnnule,
            'PBX_EFFECTUE' => $this->urlRetourEffectue,
            'PBX_REFUSE' => $this->urlRetourRefuse,
            'PBX_RETOUR' => self::RETOUR,
            'PBX_HASH' => self::HASH,
            'PBX_TIME' => $dateTime,
            'PBX_SOURCE' => self::SOURCE,
            'PBX_TYPEPAIEMENT' => self::TYPE_PAIEMENT,
            'PBX_TYPECARTE' => self::TYPECARTE,
            'PBX_REPONDRE_A' => $this->urlRepondreA,
        ];

        // ici on utilise pas http_build_query, on ne veux pas encoder les caractères
        $preparedKeys = [];
        foreach ($inputs as $key => $value) {
            if (null === $value) {
                continue;
            }
            $sanitizedInputs[$key] = $value;
            $preparedKeys[] = $key . '=' . $value;
        }

        $msg = implode('&', $preparedKeys);


        // On récupère la clé secrète HMAC (stockée dans une base de données par exemple) et que l’on renseigne dans la variable $keyTest;
        // Si la clé est en ASCII, On la transforme en binaire
        $binKey = pack("H*", $this->secretKey);

        // On calcule l’empreinte (à renseigner dans le paramètre PBX_HMAC) grâce à la fonction hash_hmac et // la clé binaire
        // On envoie via la variable PBX_HASH l'algorithme de hachage qui a été utilisé (SHA512 dans ce cas)
        $hmac = strtoupper(hash_hmac('sha512', $msg, $binKey));

        // La chaîne sera envoyée en majuscules, d'où l'utilisation de strtoupper()
        // On crée le formulaire à envoyer à Paybox System
        // ATTENTION : l'ordre des champs est extrêmement important, il doit
        // correspondre exactement à l'ordre des champs dans la chaîne hachée
        $sanitizedInputs['PBX_HMAC'] = $hmac;

        $htmlForm = '<form method="POST" action="https://' . $this->domainServer . '/cgi/MYchoix_pagepaiement.cgi">' . PHP_EOL;
        foreach ($sanitizedInputs as $inputKey => $inputValue) {
            $htmlForm .= '  <input type="hidden" name="' . $inputKey . '" value="' . $inputValue . '">' . PHP_EOL;
        }
        $htmlForm .= '  <button type="submit" class="button button--call-to-action paiement">Régler par carte</button>' . PHP_EOL;
        $htmlForm .= '</form>';

        return $htmlForm;
    }

    /**
     * @param int $total Montant en centimes d'euros
     *
     * @return self
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @param null|string $cmd
     *
     * @return self
     */
    public function setCmd($cmd)
    {
        $this->cmd = $cmd;

        return $this;
    }

    /**
     * @param null|string $porteur
     *
     * @return self
     */
    public function setPorteur($porteur)
    {
        $this->porteur = $porteur;

        return $this;
    }

    /**
     * @param null|string $urlRetourEffectue
     *
     * @return self
     */
    public function setUrlRetourEffectue($urlRetourEffectue)
    {
        $this->urlRetourEffectue = $urlRetourEffectue;

        return $this;
    }

    /**
     * @param null|string $urlRetourRefuse
     *
     * @return self
     */
    public function setUrlRetourRefuse($urlRetourRefuse)
    {
        $this->urlRetourRefuse = $urlRetourRefuse;

        return $this;
    }

    /**
     * @param null|string $urlRetourErreur
     *
     * @return self
     */
    public function setUrlRetourErreur($urlRetourErreur)
    {
        $this->urlRetourErreur = $urlRetourErreur;

        return $this;
    }

    /**
     * @param null|string $urlRetourAnnule
     *
     * @return self
     */
    public function setUrlRetourAnnule($urlRetourAnnule)
    {
        $this->urlRetourAnnule = $urlRetourAnnule;

        return $this;
    }

    /**
     * @param null|string $urlRepondreA
     *
     * return self
     */
    public function setUrlRepondreA($urlRepondreA)
    {
        $this->urlRepondreA = $urlRepondreA;

        return $this;
    }
}
