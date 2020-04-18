<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DevCallBackPayboxCotisationCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $help = <<<EOF
Cette commande simplifie l'appel au callback de paybox.
Il faut dans le container l'appeller avec un argument comme celui-ci "ttps://localhost:9206/pages/administration/paybox_effectue.php?total=3000&cmd=C2020-150120200752-0-770-GALLO-DCB&autorisation=XXXXXX&transaction=587904761&status=00000"
Il correspond à l'URL de la page de retour de paiement.
EOF;

        $this
            ->setName('dev:callback-paybox-cotisation')
            ->addArgument('url_paiement_effectue')
            ->setHelp($help)
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $parsedUrl = parse_url($input->getArgument('url_paiement_effectue'));

        $query = $parsedUrl['query'];

        $params = parse_query($query);

        $callBackParameters = [
            'total' => $params['total'],
            'cmd' => $params['cmd'],
            'autorisation' => $params['autorisation'],
            'transaction' => $params['transaction'],
            'status' => $params['status'],
        ];


        $url = 'http://localhost:80/association/paybox-callback?' . http_build_query($callBackParameters);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_exec($curl);

        $output->writeln("Appel au callback de paiement de cotisation effectué");
    }
}
