<?php

declare(strict_types=1);

namespace AppBundle\Payment;

class Paybox
{
    const DEVISE_EURO = 978;

    const SPECIAL_CHARS_INPUTS = [
        'PBX_SHOPPINGCART',
        'PBX_BILLING',
    ];

    const PAYBOX_SHOPPING_MAX_QUANTITY = 99;
    const PAYBOX_DEFAULT_STRING = 'Inconnu';
    const PAYBOX_DEFAULT_COUNTRY = 250; // correspond à la France en iso3166

    /**
     * cf http://www.paybox.com/espace-integrateur-documentation/dictionnaire-des-donnees/paybox-system/
     */
    const RETOUR = 'total:M;cmd:R;autorisation:A;transaction:T;status:E';
    const SOURCE = 'HTML';
    const HASH = 'SHA512';
    const TYPE_PAIEMENT = 'CARTE';
    const TYPECARTE = 'CB';

    private $total = 0;
    private $cmd;
    private $porteur;
    private $urlRetourEffectue;
    private $urlRetourRefuse;
    private $urlRetourAnnule;
    private $urlRepondreA;

    public function __construct(
        private $domainServer,
        private $secretKey,
        private $site,
        private $rang,
        private $identifiant,
    ) {
    }

    public function generate(\DateTimeInterface $now, PayboxBilling $payboxBilling, $quantity = 1): string
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
            'PBX_SHOPPINGCART' => $this->generatePbxShoppingcart($quantity),
            'PBX_BILLING' => $this->generatePbxBiling($payboxBilling),
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
            if (in_array($inputKey, self::SPECIAL_CHARS_INPUTS)) {
                $inputValue = htmlspecialchars((string) $inputValue);
            }
            $htmlForm .= '  <input type="hidden" name="' . $inputKey . '" value="' . $inputValue . '">' . PHP_EOL;
        }
        $htmlForm .= '  <button type="submit" class="button button--call-to-action paiement">Régler par carte</button>' . PHP_EOL;

        return $htmlForm . '</form>';
    }

    private function generatePbxShoppingcart($quantity = 1): string
    {
        if ($quantity > self::PAYBOX_SHOPPING_MAX_QUANTITY) {
            $quantity = self::PAYBOX_SHOPPING_MAX_QUANTITY;
        }

        return sprintf('<?xml version="1.0" encoding="utf-8"?><shoppingcart><total><totalQuantity>%d</totalQuantity></total></shoppingcart>', $quantity);
    }

    private function generatePbxBiling(PayboxBilling $payboxBilling): string
    {
        $domDocument = new \DOMDocument('1.0', 'utf-8');
        $billing = $domDocument->createElement('Billing');
        $domDocument->appendChild($billing);

        $address = $domDocument->createElement('Address');
        $billing->appendChild($address);

        $firstName = $domDocument->createElement('FirstName');
        $firstName->nodeValue = $this->preparePbxBillingValue($payboxBilling->getFirstName(), 30, self::PAYBOX_DEFAULT_STRING);

        $lastName = $domDocument->createElement('LastName');
        $lastName->nodeValue = $this->preparePbxBillingValue($payboxBilling->getLastName(), 30, self::PAYBOX_DEFAULT_STRING);

        $address1 = $domDocument->createElement('Address1');
        $address1->nodeValue = $this->preparePbxBillingValue($payboxBilling->getAddress1(), 50, self::PAYBOX_DEFAULT_STRING);

        $zipCode = $domDocument->createElement('ZipCode');
        $zipCode->nodeValue = $this->preparePbxBillingValue($payboxBilling->getZipCode(), 16, self::PAYBOX_DEFAULT_STRING);

        $city = $domDocument->createElement('City');
        $city->nodeValue = $this->preparePbxBillingValue($payboxBilling->getCity(), 50, self::PAYBOX_DEFAULT_STRING);

        $countryCode = $domDocument->createElement('CountryCode');
        $countryCode->nodeValue = $this->preparePbxBillingValue($payboxBilling->getCountryCodeIso3166Numeric(), 3, self::PAYBOX_DEFAULT_COUNTRY);

        $address->appendChild($firstName);
        $address->appendChild($lastName);
        $address->appendChild($address1);
        $address->appendChild($zipCode);
        $address->appendChild($city);
        $address->appendChild($countryCode);

        return str_replace(["\n", "\r"], "", $domDocument->saveXML());
    }

    private function preparePbxBillingValue($value, int $maxLength, $default)
    {
        $value = $value ? trim((string) $value) : '';

        if ($value === '') {
            $value = $default;
        }

        $value = (string) $value;
        if (strlen($value) > $maxLength) {
            $value = substr($value, 0, $maxLength);
        }

        return $value;
    }

    /**
     * @param int $total Montant en centimes d'euros
     */
    public function setTotal($total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @param null|string $cmd
     */
    public function setCmd($cmd): self
    {
        $this->cmd = $cmd;

        return $this;
    }

    /**
     * @param null|string $porteur
     */
    public function setPorteur($porteur): self
    {
        $this->porteur = $porteur;

        return $this;
    }

    /**
     * @param null|string $urlRetourEffectue
     */
    public function setUrlRetourEffectue($urlRetourEffectue): self
    {
        $this->urlRetourEffectue = $urlRetourEffectue;

        return $this;
    }

    /**
     * @param null|string $urlRetourRefuse
     */
    public function setUrlRetourRefuse($urlRetourRefuse): self
    {
        $this->urlRetourRefuse = $urlRetourRefuse;

        return $this;
    }

    /**
     * @param null|string $urlRetourErreur
     */
    public function setUrlRetourErreur($urlRetourErreur): self
    {
        return $this;
    }

    /**
     * @param null|string $urlRetourAnnule
     */
    public function setUrlRetourAnnule($urlRetourAnnule): self
    {
        $this->urlRetourAnnule = $urlRetourAnnule;

        return $this;
    }

    /**
     * @param null|string $urlRepondreA
     *
     * return self
     */
    public function setUrlRepondreA($urlRepondreA): self
    {
        $this->urlRepondreA = $urlRepondreA;

        return $this;
    }
}
