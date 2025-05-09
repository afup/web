<?php

declare(strict_types=1);

namespace AppBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DevCallBackPayboxCotisationCommand extends Command
{
    protected function configure(): void
    {
        $help = <<<EOF
Cette commande simplifie l'appel au callback de paybox.
Il faut dans le container l'appeller avec un argument comme celui-ci "https://localhost:9206/association/paybox-redirect?total=3000&cmd=C2020-150120200752-0-770-GALLO-DCB&autorisation=XXXXXX&transaction=587904761&status=00000"
Il correspond à l'URL de la page de retour de paiement.
EOF;

        $this
            ->setName('dev:callback-paybox-cotisation')
            ->addArgument('url_paiement_effectue', InputArgument::REQUIRED)
            ->setHelp($help);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $parsedUrl = parse_url((string) $input->getArgument('url_paiement_effectue'));

        $query = $parsedUrl['query'];

        parse_str($query, $params);

        $callBackParameters = [
            'total' => $params['total'],
            'cmd' => $params['cmd'],
            'autorisation' => $params['autorisation'],
            'transaction' => $params['transaction'],
            'status' => $params['status'],
        ];

        $url = 'https://apachephp:80/association/paybox-callback?' . http_build_query($callBackParameters);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_exec($curl);

        $output->writeln("Appel au callback de paiement de cotisation effectué");

        return Command::SUCCESS;
    }
}
