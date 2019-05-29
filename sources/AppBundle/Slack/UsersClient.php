<?php

namespace AppBundle\Slack;


use AppBundle\Association\Model\Repository\UserRepository;

class UsersClient
{
    const USER_LIST_API = '/users.list';

    /**
     * @var string
     */
    private $token;
    /**
     * @var string
     */
    private $apiBaseUrl;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * UsersClient constructor.
     * @param string $token Token des API Slack
     * @param string $apiBaseUrl URL de base des API Slack
     * @param UserRepository $userRepository
     */
    public function __construct($token, $apiBaseUrl, UserRepository $userRepository)
    {
        $this->token = $token;
        $this->apiBaseUrl = $apiBaseUrl;
        $this->userRepository = $userRepository;
    }

    /**
     * Retourne la liste des utilisateurs devant être supprimé du slack membre
     * @return array
     * @throws \CCMBenchmark\Ting\Query\QueryException
     */
    public function checkUsersValidity()
    {
        $result = [];
        $cursor = '';
        $today = new \DateTimeImmutable();
        do {
            //Récupère une page d'utilisateur dans Slack
            $page = $this->send($cursor);
            foreach ($page['members'] as $user) {
                // Ne traite pas les utilisateurs sans adresse courriel ou supprimé
                if (!isset($user['profile']['email']) || $user['deleted'] === true) {
                    continue;
                }
                $email = $user['profile']['email'];
                $userInfo = [
                    'slack_username' => $user['name'],
                    'slack_realname' => $user['real_name'],
                    'slack_email' => $email,
                    'user_found' => false,
                    'afup_last_subscription' => null,
                    'afup_status' => null,
                ];
                //Vérification de l'utilisateur Slack dans la base du site
                try {
                    $userDb = $this->userRepository->loadUserByUsername($email);
                    $userInfo['afup_last_subscription']=$userDb->getLastSubscription();
                    $userInfo['afup_status']=$userDb->getStatus();
                    if ($userDb->getLastSubscription() < $today || $userDb->getStatus() !== $userDb::STATUS_ACTIVE) {
                        //Utilisateur inactif ou sans souscription : a supprimer
                        $result[] = $userInfo;
                    }
                } catch (\Symfony\Component\Security\Core\Exception\UsernameNotFoundException $e) {
                    //User Not found ! A supprimer de slack !
                    $result[] = $userInfo;
                }
            }
            //Récupère le curseur pour la page suivante
            $cursor = $page['response_metadata']['next_cursor'];
        } while ($cursor !== '');
        return $result;
    }

    /**
     * Retourne une page d'utilisateur Slack
     * @param string $cursor curseur pour obtenir la page suivante des utilisateurs Slack
     * @return array
     */
    private function send($cursor = '')
    {
        $return = file_get_contents(sprintf("%s%s?token=%s&limit=100&cursor=%s", $this->apiBaseUrl, self::USER_LIST_API, $this->token, $cursor));
        if (false === $return) {
            throw new \RuntimeException("Erreur lors de l'appel à l'API slack");
        }
        $decodedContent = json_decode($return, true);
        if (false === $decodedContent) {
            throw new \RuntimeException("Erreur lecture retour API slack");
        }
        if (false === $decodedContent["ok"]) {
            throw new \RuntimeException(sprintf("Erreur sur le retour de l'appel slack : %s",
                $decodedContent['error']));
        }
        return $decodedContent;
    }
}
