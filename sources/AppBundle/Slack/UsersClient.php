<?php

declare(strict_types=1);

namespace AppBundle\Slack;

use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final readonly class UsersClient
{
    public const USER_LIST_API = '/users.list';

    public function __construct(
        #[Autowire('%slack_membre_token%')]
        private string $token,
        #[Autowire('%slack_api_url%')]
        private string $apiBaseUrl,
    ) {}

    /**
     * Retourne une page d'utilisateur Slack
     * @param string $cursor curseur pour obtenir la page suivante des utilisateurs Slack
     * @return array
     * @throws RuntimeException
     */
    public function loadPage($cursor = '')
    {
        $return = file_get_contents(sprintf("%s%s?token=%s&limit=100&cursor=%s", $this->apiBaseUrl, self::USER_LIST_API, $this->token, $cursor));
        if (false === $return) {
            throw new RuntimeException("Erreur lors de l'appel à l'API slack");
        }
        $decodedContent = json_decode($return, true);
        if (false === $decodedContent) {
            throw new RuntimeException("Erreur lecture retour API slack");
        }
        if (false === $decodedContent["ok"]) {
            throw new RuntimeException(sprintf("Erreur sur le retour de l'appel slack : %s",
                $decodedContent['error']));
        }
        return $decodedContent;
    }
}
