<?php

namespace AppBundle\Slack;

use AppBundle\Association\Model\Repository\UserRepository;

class UsersChecker
{
    /**
     * @var UsersClient
     */
    private $usersClient;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UsersClient $usersClient, UserRepository $userRepository)
    {
        $this->usersClient = $usersClient;
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
            $page = $this->usersClient->loadPage($cursor);
            foreach ($page['members'] as $user) {
                // Ne traite pas les utilisateurs sans adresse courriel ou supprimé
                if (!isset($user['profile']['email']) || $user['deleted'] === true || $user['is_admin'] === true) {
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
}
