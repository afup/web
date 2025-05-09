<?php

declare(strict_types=1);

namespace AppBundle\Tests\Payment;

use AppBundle\Payment\Paybox;
use AppBundle\Payment\PayboxBilling;
use PHPUnit\Framework\TestCase;

final class PayboxTest extends TestCase
{
    /**
     * @dataProvider generateDateProvider
     */
    public function testGenerate(
        string $domainServer,
        string $secretKey,
        string $site,
        string $rang,
        string $identifiant,
        \DateTimeImmutable $currentDate,
        \Closure $callback,
        PayboxBilling $billing,
        string $expected,
    ): void {
        $paybox = new Paybox($domainServer, $secretKey, $site, $rang, $identifiant);

        $callback($paybox);

        $actual = $paybox->generate($currentDate, $billing);

        self::assertEquals($expected, $actual);
    }

    public function generateDateProvider(): array
    {
        $casGeneral = <<<EOF
<form method="POST" action="https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi">
  <input type="hidden" name="PBX_SITE" value="1999888">
  <input type="hidden" name="PBX_RANG" value="32">
  <input type="hidden" name="PBX_IDENTIFIANT" value="110647233">
  <input type="hidden" name="PBX_TOTAL" value="2500">
  <input type="hidden" name="PBX_DEVISE" value="978">
  <input type="hidden" name="PBX_LANGUE" value="FRA">
  <input type="hidden" name="PBX_CMD" value="TEST Paybox">
  <input type="hidden" name="PBX_PORTEUR" value="test@paybox.com">
  <input type="hidden" name="PBX_RETOUR" value="total:M;cmd:R;autorisation:A;transaction:T;status:E">
  <input type="hidden" name="PBX_HASH" value="SHA512">
  <input type="hidden" name="PBX_TIME" value="2018-03-02T20:20:19+01:00">
  <input type="hidden" name="PBX_SOURCE" value="HTML">
  <input type="hidden" name="PBX_TYPEPAIEMENT" value="CARTE">
  <input type="hidden" name="PBX_TYPECARTE" value="CB">
  <input type="hidden" name="PBX_SHOPPINGCART" value="&lt;?xml version=&quot;1.0&quot; encoding=&quot;utf-8&quot;?&gt;&lt;shoppingcart&gt;&lt;total&gt;&lt;totalQuantity&gt;1&lt;/totalQuantity&gt;&lt;/total&gt;&lt;/shoppingcart&gt;">
  <input type="hidden" name="PBX_BILLING" value="&lt;?xml version=&quot;1.0&quot; encoding=&quot;utf-8&quot;?&gt;&lt;Billing&gt;&lt;Address&gt;&lt;FirstName&gt;Inconnu&lt;/FirstName&gt;&lt;LastName&gt;Inconnu&lt;/LastName&gt;&lt;Address1&gt;Inconnu&lt;/Address1&gt;&lt;ZipCode&gt;Inconnu&lt;/ZipCode&gt;&lt;City&gt;Inconnu&lt;/City&gt;&lt;CountryCode&gt;250&lt;/CountryCode&gt;&lt;/Address&gt;&lt;/Billing&gt;">
  <input type="hidden" name="PBX_HMAC" value="6391DD0A5051FEC38C0B5C2A016FB98B3423BF40FDA30213752F07F1AC4FB79F9B19BFF4C797736F7B8796DDBEFC8EAF63BE47B0F337C28D11CABA4280FF3FE0">
  <button type="submit" class="button button--call-to-action paiement">Régler par carte</button>
</form>
EOF;

        $avecBilling = <<<EOF
<form method="POST" action="https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi">
  <input type="hidden" name="PBX_SITE" value="1999888">
  <input type="hidden" name="PBX_RANG" value="32">
  <input type="hidden" name="PBX_IDENTIFIANT" value="110647233">
  <input type="hidden" name="PBX_TOTAL" value="2500">
  <input type="hidden" name="PBX_DEVISE" value="978">
  <input type="hidden" name="PBX_LANGUE" value="FRA">
  <input type="hidden" name="PBX_CMD" value="TEST Paybox">
  <input type="hidden" name="PBX_PORTEUR" value="test@paybox.com">
  <input type="hidden" name="PBX_RETOUR" value="total:M;cmd:R;autorisation:A;transaction:T;status:E">
  <input type="hidden" name="PBX_HASH" value="SHA512">
  <input type="hidden" name="PBX_TIME" value="2018-03-02T20:20:19+01:00">
  <input type="hidden" name="PBX_SOURCE" value="HTML">
  <input type="hidden" name="PBX_TYPEPAIEMENT" value="CARTE">
  <input type="hidden" name="PBX_TYPECARTE" value="CB">
  <input type="hidden" name="PBX_SHOPPINGCART" value="&lt;?xml version=&quot;1.0&quot; encoding=&quot;utf-8&quot;?&gt;&lt;shoppingcart&gt;&lt;total&gt;&lt;totalQuantity&gt;1&lt;/totalQuantity&gt;&lt;/total&gt;&lt;/shoppingcart&gt;">
  <input type="hidden" name="PBX_BILLING" value="&lt;?xml version=&quot;1.0&quot; encoding=&quot;utf-8&quot;?&gt;&lt;Billing&gt;&lt;Address&gt;&lt;FirstName&gt;Jean&lt;/FirstName&gt;&lt;LastName&gt;Maurice&lt;/LastName&gt;&lt;Address1&gt;20 rue des fleurs&lt;/Address1&gt;&lt;ZipCode&gt;75003&lt;/ZipCode&gt;&lt;City&gt;Paris&lt;/City&gt;&lt;CountryCode&gt;250&lt;/CountryCode&gt;&lt;/Address&gt;&lt;/Billing&gt;">
  <input type="hidden" name="PBX_HMAC" value="FAC38D4D5393F54D5AB200CF37A87CAA086BBEBD424236A78DDF8006A57CF0E105E2C54F5365794E1B412B9D56ADE614D0ED709C146FCAE6F5F3A304B7394ADE">
  <button type="submit" class="button button--call-to-action paiement">Régler par carte</button>
</form>
EOF;

        $urlRetour = <<<EOF
<form method="POST" action="https://preprod-tpeweb.paybox.com/cgi/MYchoix_pagepaiement.cgi">
  <input type="hidden" name="PBX_SITE" value="1999888">
  <input type="hidden" name="PBX_RANG" value="32">
  <input type="hidden" name="PBX_IDENTIFIANT" value="110647233">
  <input type="hidden" name="PBX_TOTAL" value="2500">
  <input type="hidden" name="PBX_DEVISE" value="978">
  <input type="hidden" name="PBX_LANGUE" value="FRA">
  <input type="hidden" name="PBX_CMD" value="TEST Paybox">
  <input type="hidden" name="PBX_PORTEUR" value="test@paybox.com">
  <input type="hidden" name="PBX_ANNULE" value="http://test2.com">
  <input type="hidden" name="PBX_EFFECTUE" value="http://test4.com">
  <input type="hidden" name="PBX_REFUSE" value="http://test3.com">
  <input type="hidden" name="PBX_RETOUR" value="total:M;cmd:R;autorisation:A;transaction:T;status:E">
  <input type="hidden" name="PBX_HASH" value="SHA512">
  <input type="hidden" name="PBX_TIME" value="2018-03-02T20:20:19+01:00">
  <input type="hidden" name="PBX_SOURCE" value="HTML">
  <input type="hidden" name="PBX_TYPEPAIEMENT" value="CARTE">
  <input type="hidden" name="PBX_TYPECARTE" value="CB">
  <input type="hidden" name="PBX_REPONDRE_A" value="http://reponseA">
  <input type="hidden" name="PBX_SHOPPINGCART" value="&lt;?xml version=&quot;1.0&quot; encoding=&quot;utf-8&quot;?&gt;&lt;shoppingcart&gt;&lt;total&gt;&lt;totalQuantity&gt;1&lt;/totalQuantity&gt;&lt;/total&gt;&lt;/shoppingcart&gt;">
  <input type="hidden" name="PBX_BILLING" value="&lt;?xml version=&quot;1.0&quot; encoding=&quot;utf-8&quot;?&gt;&lt;Billing&gt;&lt;Address&gt;&lt;FirstName&gt;Inconnu&lt;/FirstName&gt;&lt;LastName&gt;Inconnu&lt;/LastName&gt;&lt;Address1&gt;Inconnu&lt;/Address1&gt;&lt;ZipCode&gt;Inconnu&lt;/ZipCode&gt;&lt;City&gt;Inconnu&lt;/City&gt;&lt;CountryCode&gt;250&lt;/CountryCode&gt;&lt;/Address&gt;&lt;/Billing&gt;">
  <input type="hidden" name="PBX_HMAC" value="7D2F79EDA8203C787342BE63218D208F490646877100E7EDCFDE9ACC4757BCC21C2BA634A1E48E00C5F4999FB1347E44C7A3D7367A23E33B0C5147CDF6FC95DE">
  <button type="submit" class="button button--call-to-action paiement">Régler par carte</button>
</form>
EOF;

        $preprodTestSecretKey = '0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF';
        $preprodDomainServer = 'preprod-tpeweb.paybox.com';
        $testSite = '1999888';
        $testRang = '32';
        $testIdentifiant = '110647233';

        $billingEmpty = new PayboxBilling('', '', '', '', '', '');
        $billing = new PayboxBilling('Jean', 'Maurice', '20 rue des fleurs', '75003', 'Paris', 'FR');

        return [
            'Cas général' => [
                'domain_server' => $preprodDomainServer,
                'secret_key' => $preprodTestSecretKey,
                'site' => $testSite,
                'rang' => $testRang,
                'identifiant' => $testIdentifiant,
                'current_date' => new \DateTimeImmutable('2018-03-02 20:20:19'),
                'callback' => function (Paybox $paybox): void {
                    $paybox->setTotal(2500);
                    $paybox->setCmd('TEST Paybox');
                    $paybox->setPorteur('test@paybox.com');
                },
                'paybox_billing' => $billingEmpty,
                'expected' => $casGeneral,
            ],
            'Avec un billing' => [
                'domain_server' => $preprodDomainServer,
                'secret_key' => $preprodTestSecretKey,
                'site' => $testSite,
                'rang' => $testRang,
                'identifiant' => $testIdentifiant,
                'current_date' => new \DateTimeImmutable('2018-03-02 20:20:19'),
                'callback' => function (Paybox $paybox): void {
                    $paybox->setTotal(2500);
                    $paybox->setCmd('TEST Paybox');
                    $paybox->setPorteur('test@paybox.com');
                },
                'paybox_billing' => $billing,
                'expected' => $avecBilling,
            ],
            'URL de retour' => [
                'domain_server' => $preprodDomainServer,
                'secret_key' => $preprodTestSecretKey,
                'site' => $testSite,
                'rang' => $testRang,
                'identifiant' => $testIdentifiant,
                'current_date' => new \DateTimeImmutable('2018-03-02 20:20:19'),
                'callback' => function (Paybox $paybox): void {
                    $paybox->setTotal(2500);
                    $paybox->setCmd('TEST Paybox');
                    $paybox->setPorteur('test@paybox.com');
                    $paybox->setUrlRetourErreur('http://test.com');
                    $paybox->setUrlRetourAnnule('http://test2.com');
                    $paybox->setUrlRetourRefuse('http://test3.com');
                    $paybox->setUrlRetourEffectue('http://test4.com');
                    $paybox->setUrlRepondreA('http://reponseA');
                },
                'paybox_billing' => $billingEmpty,
                'expected' => $urlRetour,
            ],
        ];
    }
}
