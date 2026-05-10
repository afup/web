<?php

declare(strict_types=1);

namespace AppBundle\Slack;

class LegacyClient
{
    public function __construct(private $token) {}

    public function invite($email): void
    {
        $url = 'https://slack.com/api/users.admin.invite?' . http_build_query([
            'token' => $this->token,
            'email' => $email,
        ]);
        $return = file_get_contents($url);

        if (false === $return) {
            throw new \RuntimeException("Erreur lors de l'appel à l'API slack");
        }

        $decodedContent = json_decode($return, true);

        if (false === $decodedContent) {
            throw new \RuntimeException("Erreur lecture retour API slack");
        }

        if (false === $decodedContent["ok"]) {
            throw new \RuntimeException(sprintf("Erreur sur le retour de l'appel slack : %s", $decodedContent['error']));
        }
    }
}
