<?php
/**
 * @copyright Macintoshplus (c) 2019
 * Added by : Macintoshplus at 29/05/19 21:07
 */

namespace AppBundle\Controller;


use AppBundle\Slack\UsersClient;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class AdminSlackMembreController extends Controller
{
    public function checkMembersAction()
    {
        $result = $this->get(UsersClient::class)->checkUsersValidity();
        $csv = [];
        if (count($result)>0) {
            $csv[] = implode(';', array_keys($result[0]));
            foreach ($result as $user) {
                $user['user_found'] = $user['user_found']? 'true':'false';
                $csv[] = implode(';', $user);
            }
        }

        return new Response(implode("\n", $csv), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Utilisateurs a désactiver du '.date('Y-m-d h_i_s').'.csv"',
            'Expires' => '0',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Content-Transfer-Encoding' => 'binary',
        ]);
    }
}
