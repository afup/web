<?php

namespace AppBundle\Payment\tests\units;

use AppBundle\Payment\Paybox as TestedClass;

class Paybox extends \atoum
{
    /**
     * @dataProvider generateDateProvider
     */
    public function testGenerate($case, $domainServer, $secretKey, $site, $rang, $identifiant, $currentDate, $callback, $expected)
    {
        $this
            ->assert($case)
            ->when($paybox = new TestedClass($domainServer, $secretKey, $site, $rang, $identifiant))
            ->and($callback($paybox))
            ->then
            ->string($paybox->generate($currentDate))
                ->isEqualTo($expected, $case)
        ;
    }

    protected function generateDateProvider()
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
  <input type="hidden" name="PBX_HMAC" value="39EE89FB543A226318139335D24074868FA5912418045813F225572BD1FE069AC88C6B70D0BA1B84B2A974F0D22572D2FFFA3D309E2F2192CEF12E44931CA88C">
  <INPUT TYPE=SUBMIT class="button button--call-to-action" VALUE="R&eacute;gler par carte">
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
  <input type="hidden" name="PBX_HMAC" value="8E5F8C3C052D64606288F3C55C502153CA1D424F7C25CAD4057100897F95E481F155DBD349495FD4C327F5389BA6C98C3DB43A3CF5394378F4276EB53A1C0245">
  <INPUT TYPE=SUBMIT class="button button--call-to-action" VALUE="R&eacute;gler par carte">
</form>
EOF;

        $preprodTestSecretKey = '0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF';
        $preprodDomainServer = 'preprod-tpeweb.paybox.com';
        $testSite = '1999888';
        $testRang = '32';
        $testIdentifiant = '110647233';

        return [
            [
                'case' => 'Cas général',
                'domain_server' => $preprodDomainServer,
                'secret_key' => $preprodTestSecretKey,
                'site' => $testSite,
                'rang' => $testRang,
                'identifiant' => $testIdentifiant,
                'current_date' => new \DateTimeImmutable('2018-03-02 20:20:19'),
                'callback' => function(TestedClass $paybox) {
                    $paybox->setTotal(2500);
                    $paybox->setCmd('TEST Paybox');
                    $paybox->setPorteur('test@paybox.com');
                },
                'expected' => $casGeneral,
            ],
            [
                'case' => 'URL de retour',
                'domain_server' => $preprodDomainServer,
                'secret_key' => $preprodTestSecretKey,
                'site' => $testSite,
                'rang' => $testRang,
                'identifiant' => $testIdentifiant,
                'current_date' => new \DateTimeImmutable('2018-03-02 20:20:19'),
                'callback' => function(TestedClass $paybox) {
                    $paybox->setTotal(2500);
                    $paybox->setCmd('TEST Paybox');
                    $paybox->setPorteur('test@paybox.com');
                    $paybox->setUrlRetourErreur('http://test.com');
                    $paybox->setUrlRetourAnnule('http://test2.com');
                    $paybox->setUrlRetourRefuse('http://test3.com');
                    $paybox->setUrlRetourEffectue('http://test4.com');
                    $paybox->setUrlRepondreA('http://reponseA');
                },
                'expected' => $urlRetour,
            ],
        ];
    }
}
